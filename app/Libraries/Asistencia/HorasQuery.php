<?php
namespace App\Libraries\Asistencia;

/**
 * Resumen de HORAS por persona para /admin/horas.
 *
 * Por cada integrante del equipo calcula, en un rango de fechas:
 *   · horas reales trabajadas (entrada → salida)
 *   · horas programadas según su horario (días laborables − festivos)
 *   · saldo (reales − programadas): extra o pendiente
 *   · tardanzas, faltas y jornadas sin salida
 *
 * Las filas con entrada pero sin salida se estiman hasta la hora de
 * salida del horario y se contabilizan aparte (`sin_salida`), para que
 * el total no se caiga a cero por olvidar marcar.
 */
class HorasQuery
{
    /** Días laborables por defecto cuando la persona no tiene horario. */
    private const DIAS_DEFECTO = [1, 2, 3, 4, 5];

    /**
     * @param array $filas    Filas de attendance ya filtradas por rango/admin
     * @param array $personal Personas del admin (con schedule_id, role, ...)
     * @param array $horarios Schedules indexados por id
     * @param array $festivos Fechas festivas del rango
     *
     * @return array{filas: array, total: array} `filas` = una por persona,
     *         `total` = agregado de todo el equipo.
     */
    public function porEmpleado(
        array $filas,
        array $personal,
        array $horarios,
        array $festivos,
        string $fi,
        string $ff
    ): array {
        // Índice de marcaciones por persona
        $porUsuario = [];
        foreach ($filas as $f) {
            $porUsuario[(int) ($f['user_id'] ?? 0)][] = $f;
        }
        $festivoSet = array_flip(array_map('strval', $festivos));

        $salida = [];
        foreach ($personal as $p) {
            $uid     = (int) ($p['id'] ?? 0);
            $horario = $horarios[(int) ($p['schedule_id'] ?? 0)] ?? null;
            $salida[] = $this->resumirPersona(
                $p,
                $porUsuario[$uid] ?? [],
                $horario,
                $festivoSet,
                $fi,
                $ff
            );
        }

        // Ordena: mayor saldo primero (las horas extra arriba)
        usort($salida, static fn (array $a, array $b) => $b['saldo_min'] <=> $a['saldo_min']);

        return ['filas' => $salida, 'total' => $this->agregar($salida)];
    }

    /** Resumen de UNA persona. */
    private function resumirPersona(
        array $p,
        array $marcaciones,
        ?array $horario,
        array $festivos,
        string $fi,
        string $ff
    ): array {
        $horasTrab = 0;   // minutos reales
        $estimadas = 0;   // jornadas que se estimaron por falta de salida
        $presentes = 0;
        $tardanzas = 0;
        $faltas    = 0;
        $sinSalida = 0;

        $jornadaMin = $this->jornadaMinutos($horario);
        $salidaPlan = $horario ? (string) ($horario['hora_salida'] ?? '') : '';

        foreach ($marcaciones as $m) {
            $estado = (string) ($m['status'] ?? '');
            $tin    = (string) ($m['time_in'] ?? '');
            $tout   = (string) ($m['time_out'] ?? '');

            if ($estado === 'absent' || $estado === 'absent_justified') {
                $faltas++;
                continue;
            }
            if ($tin === '') {
                // Sin entrada: no aporta horas
                continue;
            }

            $presentes++;
            if ($estado === 'late') {
                $tardanzas++;
            }

            if ($tout !== '') {
                $horasTrab += $this->diferenciaMinutos($tin, $tout);
                continue;
            }

            // Entrada sin salida: se estima hasta la salida del horario
            $sinSalida++;
            if ($salidaPlan !== '') {
                $entTs = strtotime('2000-01-01 ' . $tin);
                $plnTs = strtotime('2000-01-01 ' . $salidaPlan);
                // Sólo un turno que cruza la medianoche (salida < entrada)
                // justifica el envoltorio: quien entró después de la salida
                // prevista (18:01 en jornada 08–13) no suma horas imaginarias.
                $noche = $entTs !== false && $plnTs !== false
                    && $plnTs < strtotime('2000-01-01 ' . (string) ($horario['hora_entrada'] ?? '00:00:00'));

                $min = $this->diferenciaMinutos($tin, $salidaPlan);
                if (! $noche && $entTs !== false && $plnTs !== false && $plnTs <= $entTs) {
                    $min = 0;
                }
                if ($min > 0 && $min <= 24 * 60) {
                    $horasTrab += $min;
                    $estimadas++;
                }
            }
        }

        $diasLab    = $this->diasLaborables($fi, $ff, $horario, $festivos);
        $programadas = ($jornadaMin > 0 && $diasLab > 0) ? $jornadaMin * $diasLab : 0;
        $saldo       = $horasTrab - $programadas;

        return [
            'user_id'       => (int) ($p['id'] ?? 0),
            'name'          => (string) ($p['name'] ?? ''),
            'dni'           => (string) ($p['dni'] ?? ''),
            'role'          => (string) ($p['role'] ?? ''),
            'cargo'         => (string) ($p['cargo'] ?? ''),
            'horario'       => $horario ? (string) ($horario['nombre'] ?? '') : '',
            'horario_txt'   => $this->horarioTexto($horario),
            'dias_laborables' => $diasLab,
            'dias_trabajados' => $presentes,
            'horas_reales'    => $horasTrab,
            'horas_programadas' => $programadas,
            'saldo_min'       => $saldo,
            'estimadas'       => $estimadas,
            'tardanzas'       => $tardanzas,
            'faltas'          => $faltas,
            'sin_salida'      => $sinSalida,
        ];
    }

    /** Agrega el equipo entero. */
    private function agregar(array $filas): array
    {
        $t = [
            'personas'         => count($filas),
            'dias_laborables'  => 0,
            'dias_trabajados'  => 0,
            'horas_reales'     => 0,
            'horas_programadas' => 0,
            'saldo_min'        => 0,
            'estimadas'        => 0,
            'tardanzas'        => 0,
            'faltas'           => 0,
            'sin_salida'       => 0,
        ];

        foreach ($filas as $f) {
            foreach (['dias_laborables', 'dias_trabajados', 'horas_reales', 'horas_programadas', 'saldo_min', 'estimadas', 'tardanzas', 'faltas', 'sin_salida'] as $k) {
                $t[$k] += (int) $f[$k];
            }
        }

        return $t;
    }

    /** Minutos de jornada diaria del horario. 0 si no hay horario. */
    private function jornadaMinutos(?array $horario): int
    {
        if (! $horario) {
            return 0;
        }
        $e = (string) ($horario['hora_entrada'] ?? '');
        $s = (string) ($horario['hora_salida'] ?? '');
        if ($e === '' || $s === '') {
            return 0;
        }
        $min = $this->diferenciaMinutos($e, $s);

        return $min > 0 ? $min : 0;
    }

    /** Minutos entre dos horas 'HH:MM[:SS]' (con cruce de medianoche). */
    private function diferenciaMinutos(string $desde, string $hasta): int
    {
        $a = strtotime('2000-01-01 ' . $desde);
        $b = strtotime('2000-01-01 ' . $hasta);
        if ($a === false || $b === false) {
            return 0;
        }
        $seg = $b - $a;
        if ($seg < 0) {
            $seg += 24 * 3600; // turno nocturno
        }

        return (int) round($seg / 60);
    }

    /**
     * Días del rango que son laborables según el horario y NO festivos.
     * Sin horario se asume de lunes a viernes.
     */
    private function diasLaborables(string $fi, string $ff, ?array $horario, array $festivos): int
    {
        $dias = $this->diasSemana((string) ($horario['dias'] ?? ''));
        $ini  = strtotime($fi);
        $fin  = strtotime($ff);
        if ($ini === false || $fin === false || $fin < $ini) {
            return 0;
        }

        $n = 0;
        for ($ts = $ini; $ts <= $fin; $ts = strtotime('+1 day', $ts)) {
            $fecha = date('Y-m-d', $ts);
            if (! isset($dias[(int) date('w', $ts)])) {
                continue;
            }
            if (isset($festivos[$fecha])) {
                continue;
            }
            $n++;
        }

        return $n;
    }

    /**
     * 'Lun - Vie' → [1..5]; 'Lun, Mié, Vie' → [1,3,5].
     * Vacío o irreconocible → laborables por defecto.
     */
    public static function diasSemana(string $texto): array
    {
        $map = [
            'lun' => 1, 'lunes' => 1,
            'mar' => 2, 'martes' => 2,
            'mie' => 3, 'mié' => 3, 'miercoles' => 3, 'miércoles' => 3,
            'jue' => 4, 'jueves' => 4,
            'vie' => 5, 'viernes' => 5,
            'sab' => 6, 'sáb' => 6, 'sabado' => 6, 'sábado' => 6,
            'dom' => 0, 'domingo' => 0,
        ];

        $txt = mb_strtolower(trim($texto));
        if ($txt === '') {
            return array_flip(self::DIAS_DEFECTO);
        }

        $separado = preg_split('/[\s,;]+/u', $txt, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens   = [];
        foreach ($separado as $t) {
            $t = mb_strtolower(trim($t, " \t-–—."));
            if ($t === '' || $t === '-' || $t === 'a' || $t === 'al') {
                continue;
            }
            if (isset($map[$t])) {
                $tokens[] = $map[$t];
            }
        }

        if ($tokens === []) {
            return array_flip(self::DIAS_DEFECTO);
        }

        // Rango "lun - vie": sólo dos días marcados y se escribieron con guion
        if (count($tokens) === 2 && preg_match('/-|–|—| a | al /u', $txt)) {
            [$a, $b] = $tokens;
            $out = [];
            $d = $a;
            for ($i = 0; $i < 7; $i++) {
                $out[$d] = true;
                if ($d === $b) {
                    break;
                }
                $d = ($d + 1) % 7;
            }

            return $out;
        }

        return array_flip(array_unique($tokens));
    }

    /** Texto legible del horario: 'Lun - Vie · 08:00 → 17:00'. */
    private function horarioTexto(?array $horario): string
    {
        if (! $horario) {
            return 'Sin horario';
        }
        $e = substr((string) ($horario['hora_entrada'] ?? ''), 0, 5);
        $s = substr((string) ($horario['hora_salida'] ?? ''), 0, 5);
        $d = trim((string) ($horario['dias'] ?? ''));

        return trim(($d !== '' ? $d : '—') . ' · ' . ($e ?: '?') . ' → ' . ($s ?: '?'));
    }

    // ══════════════════════════════════════════════
    // Formato de salida (vista y CSV)
    // ══════════════════════════════════════════════

    /** 525 minutos → '8 h 45 min'. */
    public static function formatoMinutos(int $min): string
    {
        $signo = $min < 0 ? '-' : '';
        $min   = abs($min);
        $h     = intdiv($min, 60);
        $m     = $min % 60;

        return $signo . ($h > 0 ? $h . ' h' : '') . ($m > 0 || $h === 0 ? ($h > 0 ? ' ' : '') . $m . ' min' : '');
    }

    /** 525 → '8.75' (para celdas numéricas). */
    public static function horasDecimales(int $min): string
    {
        return number_format($min / 60, 2, '.', '');
    }
}
