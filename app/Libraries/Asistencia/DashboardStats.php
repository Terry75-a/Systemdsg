<?php
namespace App\Libraries\Asistencia;

/**
 * Números del dashboard del admin (réplica del dashboard de System-Trackin).
 *
 * Trabaja sobre los arreglos que ya arma AdminBaseController::viewData()
 * (asistencias, personal e incidencias del admin), así no hace consultas extra.
 *
 * A tiempo        = marcó entrada dentro de la tolerancia (present, no_exit).
 * Fuera de tiempo = tardanza o falta sin justificar (late, absent).
 * La falta justificada no cuenta para ninguno de los dos.
 * Equipo          = promedio de Empleados y Practicantes (como el HSP de Trackin).
 */
class DashboardStats
{
    /** Meta de puntualidad del equipo (%). */
    public const META = 90;

    public const MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    /** Columnas de las tablas por estado, en el orden del recorrido. */
    public const ESTADOS = [
        'present'          => 'Presente',
        'no_exit'          => 'Sin salida',
        'late'             => 'Tardanza',
        'absent'           => 'Falta',
        'absent_justified' => 'Justificada',
    ];

    public const A_TIEMPO = ['present', 'no_exit'];
    public const FUERA    = ['late', 'absent'];

    public static function servicio(string $status): ?string
    {
        if (in_array($status, self::A_TIEMPO, true)) return 'on';
        if (in_array($status, self::FUERA, true)) return 'late';
        return null;
    }

    public static function pct(int $a, int $n): ?int
    {
        return $n > 0 ? (int) round($a / $n * 100) : null;
    }

    /** Promedio de los grupos que tienen datos, sin redondear antes. */
    public static function promedio(array $pares): ?int
    {
        $r = [];
        foreach ($pares as [$a, $n]) {
            if ($n > 0) $r[] = $a / $n * 100;
        }
        return $r ? (int) round(array_sum($r) / count($r)) : null;
    }

    /**
     * @param array $get    query string (anio, areas, meses)
     * @param array $links  rutas del panel: ['asist' => ..., 'inc' => ...]
     */
    public static function build(array $attendance, array $personal, array $incidents, array $get, array $links = []): array
    {
        // Persona → rol y área
        $quien = [];
        foreach ($personal as $p) {
            $quien[(int) $p['id']] = [
                'rol'  => ($p['role'] ?? '') === 'Practicante' ? 'prac' : 'emp',
                'area' => trim((string) ($p['area'] ?? '')),
            ];
        }
        $rolDe  = fn ($uid) => $quien[(int) $uid]['rol'] ?? 'emp';
        $areaDe = fn ($uid) => $quien[(int) $uid]['area'] ?? '';

        // Años con datos (siempre incluye el actual)
        $years = [(int) date('Y')];
        foreach ($attendance as $r) {
            if (!empty($r['date'])) $years[] = (int) substr($r['date'], 0, 4);
        }
        $years = array_values(array_unique($years));
        rsort($years);
        $year = (int) ($get['anio'] ?? 0);
        if (!in_array($year, $years, true)) {
            $year = (int) date('Y');
        }

        // Áreas (chips). Sin elegir = todas.
        $centers = [];
        foreach ($personal as $p) {
            $a = trim((string) ($p['area'] ?? ''));
            if ($a !== '') $centers[$a] = true;
        }
        $centers = array_keys($centers);
        sort($centers);
        $areas = array_values(array_filter(
            array_map('trim', explode(',', (string) ($get['areas'] ?? ''))),
            fn ($a) => in_array($a, $centers, true)
        ));
        $areaSel = $areas ?: $centers;

        // Marcaciones del año filtradas por área
        $rows = array_values(array_filter($attendance, function ($r) use ($year, $areas, $areaDe) {
            if ((int) substr((string) ($r['date'] ?? ''), 0, 4) !== $year) return false;
            return !$areas || in_array($areaDe($r['user_id'] ?? 0), $areas, true);
        }));

        // Celdas mes × estado por rol: [rol][mes][status] = n
        $cells = ['emp' => [], 'prac' => []];
        foreach ($rows as $r) {
            $m  = (int) substr($r['date'], 5, 2);
            $st = (string) ($r['status'] ?? 'present');
            $cells[$rolDe($r['user_id'] ?? 0)][$m][$st] = ($cells[$rolDe($r['user_id'] ?? 0)][$m][$st] ?? 0) + 1;
        }
        $suma = function (string $rol, ?int $m, ?array $sts) use ($cells): int {
            $t = 0;
            foreach ($cells[$rol] as $mes => $porEstado) {
                if ($m !== null && $mes !== $m) continue;
                foreach ($porEstado as $st => $n) {
                    if ($sts === null || in_array($st, $sts, true)) $t += $n;
                }
            }
            return $t;
        };
        $grupo = fn (string $rol, ?int $m) => [
            $suma($rol, $m, self::A_TIEMPO),
            $suma($rol, $m, array_merge(self::A_TIEMPO, self::FUERA)),
        ];

        // Tabla «Puntualidad por mes»
        $monthStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $e = $grupo('emp', $m);
            $p = $grupo('prac', $m);
            if (!$suma('emp', $m, null) && !$suma('prac', $m, null)) continue;
            $monthStats[] = [
                'm'   => $m,
                'emp' => ['a' => $e[0], 'n' => $e[1], 'p' => self::pct($e[0], $e[1])],
                'prac'=> ['a' => $p[0], 'n' => $p[1], 'p' => self::pct($p[0], $p[1])],
                'eq'  => self::promedio([$e, $p]),
            ];
        }
        $activeMonths = array_column($monthStats, 'm');

        $mesesSel = array_values(array_filter(
            array_map('intval', explode(',', (string) ($get['meses'] ?? ''))),
            fn ($m) => in_array($m, $activeMonths, true)
        ));
        if (!$mesesSel) $mesesSel = array_slice($activeMonths, -2);
        sort($mesesSel);

        $eT = $grupo('emp', null);
        $pT = $grupo('prac', null);
        $total     = count($rows);
        $completas = count(array_filter($rows, fn ($r) => !empty($r['time_in']) && !empty($r['time_out'])));
        $sinSalida = count(array_filter($rows, fn ($r) => ($r['status'] ?? '') === 'no_exit'));

        // Tablas por estado (pivot) — sólo los meses elegidos
        $pivot = [];
        foreach (['emp', 'prac'] as $rol) {
            $meses = [];
            foreach ($mesesSel as $m) {
                if (!$suma($rol, $m, null)) continue;
                $fila = ['m' => $m, 'total' => $suma($rol, $m, null), 'on' => $suma($rol, $m, self::A_TIEMPO),
                    'late' => $suma($rol, $m, self::FUERA), 'st' => []];
                foreach (array_keys(self::ESTADOS) as $st) {
                    $fila['st'][$st] = $cells[$rol][$m][$st] ?? 0;
                }
                $meses[] = $fila;
            }
            $maxOn = $maxLate = 1;
            foreach ($meses as $f) {
                foreach ($f['st'] as $st => $n) {
                    if (self::servicio($st) === 'on')   $maxOn   = max($maxOn, $n);
                    if (self::servicio($st) === 'late') $maxLate = max($maxLate, $n);
                }
            }
            $pivot[$rol] = ['rows' => $meses, 'maxOn' => $maxOn, 'maxLate' => $maxLate];
        }

        // Motivos de incidencia por rol (año elegido)
        $reasons = ['emp' => [], 'prac' => []];
        foreach ($incidents as $inc) {
            if ((int) substr((string) ($inc['fecha'] ?? ''), 0, 4) !== $year) continue;
            if ($areas && !in_array($areaDe($inc['user_id'] ?? 0), $areas, true)) continue;
            $tipo = trim((string) ($inc['tipo'] ?? '')) ?: 'Sin motivo';
            $rol  = $rolDe($inc['user_id'] ?? 0);
            $reasons[$rol][$tipo] = ($reasons[$rol][$tipo] ?? 0) + 1;
        }
        foreach ($reasons as &$lista) arsort($lista);
        unset($lista);

        return [
            'year'        => $year,
            'years'       => $years,
            'centers'     => $centers,
            'areas'       => $areas,
            'areaSel'     => $areaSel,
            'target'      => self::META,
            'total'       => $total,
            'completas'   => $completas,
            'sinSalida'   => $sinSalida,
            'empPct'      => self::pct($eT[0], $eT[1]),
            'pracPct'     => self::pct($pT[0], $pT[1]),
            'eqPct'       => self::promedio([$eT, $pT]),
            'monthStats'  => $monthStats,
            'activeMonths'=> $activeMonths,
            'mesesSel'    => $mesesSel,
            'pivot'       => $pivot,
            'reasons'     => $reasons,
            'closing'     => self::cierre($attendance, $incidents, $links + ['asist' => 'admin/asistencias', 'inc' => 'admin/incidencias']),
        ];
    }

    /**
     * Cierre de mes: todo mes anterior al actual debería quedar sin
     * marcaciones abiertas (sin salida) ni incidencias por resolver.
     */
    private static function cierre(array $attendance, array $incidents, array $links): array
    {
        $actual = date('Y-m');
        $meses  = [$actual => ['ym' => $actual, 'total' => 0, 'completas' => 0, 'sinSalida' => 0, 'incid' => 0]];
        $nuevo  = fn ($ym) => ['ym' => $ym, 'total' => 0, 'completas' => 0, 'sinSalida' => 0, 'incid' => 0];
        $pendientes = [];

        foreach ($attendance as $r) {
            $ym = substr((string) ($r['date'] ?? ''), 0, 7);
            if ($ym === '') continue;
            $meses[$ym] ??= $nuevo($ym);
            $meses[$ym]['total']++;
            if (!empty($r['time_in']) && !empty($r['time_out'])) $meses[$ym]['completas']++;
            if (($r['status'] ?? '') === 'no_exit') {
                $meses[$ym]['sinSalida']++;
                $pendientes[] = ['tipo' => 'Sin salida', 'name' => $r['name'] ?? '', 'fecha' => $r['date'],
                    'url' => $links['asist'] . '?fecha_inicio=' . $r['date'] . '&fecha_fin=' . $r['date'] . '&estado=no_exit'];
            }
        }
        foreach ($incidents as $inc) {
            if (!in_array($inc['estado'] ?? '', ['Pendiente', 'Revisión'], true)) continue;
            $fecha = substr((string) ($inc['fecha'] ?? ''), 0, 10);
            $ym = substr($fecha, 0, 7);
            if ($ym === '') continue;
            $meses[$ym] ??= $nuevo($ym);
            $meses[$ym]['incid']++;
            $pendientes[] = ['tipo' => 'Incidencia · ' . ($inc['tipo'] ?? ''), 'name' => $inc['name'] ?? '', 'fecha' => $fecha,
                'url' => $links['inc']];
        }

        krsort($meses);
        // Meses con algo abierto + los 4 más recientes
        $lista = [];
        $i = 0;
        foreach ($meses as $ym => $m) {
            $abierto = $m['sinSalida'] + $m['incid'];
            if ($i++ >= 4 && !$abierto) continue;
            $m['open']  = $abierto;
            $m['state'] = $ym === $actual ? 'current' : ($abierto ? 'overdue' : 'closed');
            $lista[] = $m;
        }

        usort($pendientes, fn ($a, $b) => strcmp($a['fecha'], $b['fecha']));
        $hoy = strtotime(date('Y-m-d'));
        foreach ($pendientes as &$p) {
            $p['age'] = max(0, (int) round(($hoy - strtotime($p['fecha'])) / 86400));
        }
        unset($p);

        return [
            'current' => $actual,
            'months'  => $lista,
            'oldest'  => array_slice($pendientes, 0, 6),
            'overdue' => count(array_filter($lista, fn ($m) => $m['state'] === 'overdue')),
        ];
    }
}
