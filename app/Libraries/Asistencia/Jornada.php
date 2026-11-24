<?php

namespace App\Libraries\Asistencia;

/**
 * Reglas de jornada compartidas por el panel de empleado, el cierre
 * automático y los comandos CLI del módulo de asistencia.
 */
class Jornada
{
    /** Tipo de jornada de un horario (columna schedules.jornada). */
    public const FULL = 'full';
    public const PART = 'part';
    public const JORNADAS = [self::FULL => 'Full time', self::PART => 'Part time'];

    /** Desde cuántas horas al día un horario se considera full time. */
    public const HORAS_FULL = 8;

    /** Full o part time según las horas del día (suma de los dos bloques). */
    public static function detectar(?array $schedule): string
    {
        return self::minutosJornada($schedule) >= self::HORAS_FULL * 60 ? self::FULL : self::PART;
    }

    /** Jornada guardada del horario; si no tiene, la detecta por sus horas. */
    public static function jornada(?array $schedule): string
    {
        $j = (string) ($schedule['jornada'] ?? '');
        return isset(self::JORNADAS[$j]) ? $j : self::detectar($schedule);
    }

    public const DIAS = [
        1 => 'lun', 2 => 'mar', 3 => 'mie', 4 => 'jue', 5 => 'vie', 6 => 'sab', 7 => 'dom',
    ];

    /** Horario aplicable a una persona: solo de su rol, jamás de otro tipo. */
    public static function elegir(array $schedules, int $scheduleId, string $role): array
    {
        $tipoRole = $role === 'Practicante' ? 'Practicante' : 'Empleado';

        $delTipo = array_values(array_filter(
            $schedules,
            fn($s) => ($s['tipo'] ?? 'Empleado') === $tipoRole
        ));

        if ($scheduleId > 0) {
            foreach ($delTipo as $sch) {
                if ((int) ($sch['id'] ?? 0) === $scheduleId) {
                    return $sch;
                }
            }
        }

        return $delTipo[0] ?? [];
    }

    /**
     * Qué marca le toca ahora a la persona:
     *   entrada · salida_descanso · regreso · salida · completo
     *
     * En doble turno la segunda marca es la salida al descanso si llega antes
     * de la mitad del segundo bloque; si llega después (no marcó el descanso)
     * se toma como la salida final del día.
     */
    public static function siguienteMarca(?array $att, ?array $schedule, string $ahora): string
    {
        $tin = trim((string) ($att['time_in'] ?? ''));
        if (! $att || $tin === '') {
            return 'entrada';
        }
        if (trim((string) ($att['time_out'] ?? '')) !== '') {
            return 'completo';
        }
        if (trim((string) ($att['time_in2'] ?? '')) !== '') {
            return 'salida';
        }
        if (trim((string) ($att['time_out1'] ?? '')) !== '') {
            return 'regreso';
        }
        if (! self::esDobleTurno($schedule)) {
            return 'salida';
        }
        [$e2, $s2] = self::bloques($schedule)[1];
        $ini = strtotime('2000-01-01 ' . $e2);
        $fin = strtotime('2000-01-01 ' . $s2);
        $now = strtotime('2000-01-01 ' . $ahora);
        if ($ini === false || $fin === false || $now === false || $fin <= $ini) {
            return 'salida';
        }
        return $now < (int) (($ini + $fin) / 2) ? 'salida_descanso' : 'salida';
    }

    /**
     * Minutos trabajados de un registro. Si marcó el descanso se usan sus
     * marcas reales (entrada→salida descanso + regreso→salida); si no, se
     * descuenta el descanso programado del horario.
     */
    public static function minutosRegistro(array $att, ?array $schedule): int
    {
        $tin  = trim((string) ($att['time_in'] ?? ''));
        $out1 = trim((string) ($att['time_out1'] ?? ''));
        $in2  = trim((string) ($att['time_in2'] ?? ''));
        $tout = trim((string) ($att['time_out'] ?? ''));
        if ($tin === '') {
            return 0;
        }
        if ($out1 !== '') {
            $min = self::minutosEntre($tin, $out1);
            if ($in2 !== '' && $tout !== '') {
                $min += self::minutosEntre($in2, $tout);
            }
            return max(0, $min);
        }
        return $tout === '' ? 0 : self::minutosTrabajados($tin, $tout, $schedule);
    }

    /** Bloques en texto: '09:00 – 13:00 · 14:00 – 19:00'. */
    public static function textoBloques(?array $schedule): string
    {
        $out = [];
        foreach (self::bloques($schedule) as [$e, $s]) {
            $out[] = substr($e, 0, 5) . ' – ' . substr($s, 0, 5);
        }
        return implode(' · ', $out);
    }

    /**
     * Horarios de una persona en texto, uno por línea:
     *   ['Lun - Vie · 09:00 – 13:00 · 14:00 – 19:00', 'Sabado · 09:00 – 13:00']
     */
    public static function textoHorarios(array $schedules): array
    {
        $out = [];
        foreach ($schedules as $sch) {
            $dias  = trim((string) ($sch['dias'] ?? ''));
            $out[] = ($dias !== '' ? $dias . ' · ' : '') . self::textoBloques($sch);
        }
        return $out;
    }

    /** Horarios asignados a la persona (del tipo de su rol), de una lista ya cargada. */
    public static function horariosDe(array $schedules, array $user): array
    {
        $tipoRole = ((string) ($user['role'] ?? '')) === 'Practicante' ? 'Practicante' : 'Empleado';
        $porId = [];
        foreach ($schedules as $sch) $porId[(int) ($sch['id'] ?? 0)] = $sch;
        $out = [];
        foreach (HorariosPersona::ids($user) as $id) {
            if (isset($porId[$id]) && ($porId[$id]['tipo'] ?? 'Empleado') === $tipoRole) {
                $out[] = $porId[$id];
            }
        }
        return $out;
    }

    /** Días de la semana (1 = lunes … 7 = domingo) que cubre un horario. */
    public static function diasDeSemana(?array $schedule): array
    {
        $out = [];
        $lunes = strtotime('monday this week');
        for ($i = 0; $i < 7; $i++) {
            if (self::esDiaLaborable($schedule, date('Y-m-d', strtotime("+{$i} day", $lunes)))) {
                $out[] = $i + 1;
            }
        }
        return $out;
    }

    /**
     * Horario que le toca a una persona en una fecha.
     *
     * De sus horarios asignados (y del tipo de su rol) se usa el que incluye
     * ese día de la semana. Si ninguno lo incluye, se devuelve el primero
     * asignado: con él esDiaLaborable() dirá que ese día descansa.
     * Sin horarios asignados devuelve [] (no se le pone el de otra persona).
     */
    public static function delDia(array $schedules, array $user, string $fecha): array
    {
        $role     = (string) ($user['role'] ?? 'Empleado');
        $tipoRole = $role === 'Practicante' ? 'Practicante' : 'Empleado';
        $ids      = HorariosPersona::ids($user);

        $suyos = [];
        foreach ($ids as $id) {
            foreach ($schedules as $sch) {
                if ((int) ($sch['id'] ?? 0) === $id && ($sch['tipo'] ?? 'Empleado') === $tipoRole) {
                    $suyos[] = $sch;
                }
            }
        }
        if ($suyos === []) {
            // Sin horario asignado no se inventa uno: la persona no tiene horario.
            return [];
        }
        foreach ($suyos as $sch) {
            if (self::esDiaLaborable($sch, $fecha)) {
                return $sch;
            }
        }
        return $suyos[0];
    }

    /**
     * ¿Es día laborable para ese horario? Sin horario se asume Lunes a Viernes.
     */
    public static function esDiaLaborable(?array $schedule, string $fecha): bool
    {
        $num = (int) date('N', strtotime($fecha));
        if ($num === 7) { // domingo nunca laborable
            return false;
        }

        $dias = trim((string) ($schedule['dias'] ?? ''));
        if ($dias === '') {
            return $num <= 5;
        }

        $norm = self::normalizar($dias);

        // Días nombrados: "Lun - Vie", "Lunes,Martes", "lun a vie"…
        $encontrados = [];
        foreach (self::DIAS as $n => $token) {
            $p = mb_strpos($norm, $token);
            if ($p !== false) {
                $encontrados[$p] = $n;
            }
        }

        if ($encontrados !== []) {
            ksort($encontrados);
            $nums = array_values($encontrados);

            // "lun - vie", "lunes al viernes", "vie a lun" → rango (con vuelta)
            if (count($nums) === 2 && preg_match('/\s+-\s+|\s+a(?:l)?\s+|\s+hasta\s+/', $norm)) {
                return in_array($num, self::rango($nums[0], $nums[1]), true);
            }

            return in_array($num, $nums, true);
        }

        // "1-5", "1,2,3"
        if (preg_match_all('/[1-7]/', $norm, $m)) {
            return in_array($num, array_map('intval', $m[0]), true);
        }

        return $num <= 5;
    }

    /** De $desde hasta $hasta incluidos, dando la vuelta de 7 → 1. */
    private static function rango(int $desde, int $hasta): array
    {
        $salida = [$desde];
        $actual = $desde;

        while ($actual !== $hasta && count($salida) < 7) {
            $actual = $actual === 7 ? 1 : $actual + 1;
            $salida[] = $actual;
        }

        return $salida;
    }

    /** Timestamp límite de entrada (hora_base + tolerancia). */
    public static function limiteEntrada(?array $schedule): int
    {
        $hora = (string) ($schedule['hora_entrada'] ?? '08:00');
        $tol  = (int) ($schedule['tolerancia'] ?? 10);

        return strtotime($hora) + $tol * 60;
    }

    /**
     * Bloques de la jornada (jornada partida / doble turno).
     *
     * [[entrada, salida]] cuando el horario es de un solo turno y
     * [[entrada, salida], [entrada2, salida2]] cuando tiene el segundo
     * bloque (08→13 + 16→20). Un bloque incompleto se descarta.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    public static function bloques(?array $schedule): array
    {
        if (!$schedule) {
            return [];
        }

        $bloques = [];
        $e1 = trim((string) ($schedule['hora_entrada'] ?? ''));
        $s1 = trim((string) ($schedule['hora_salida'] ?? ''));
        if ($e1 !== '' && $s1 !== '') {
            $bloques[] = [$e1, $s1];
        }

        $e2 = trim((string) ($schedule['hora_entrada2'] ?? ''));
        $s2 = trim((string) ($schedule['hora_salida2'] ?? ''));
        if ($e2 !== '' && $s2 !== '') {
            $bloques[] = [$e2, $s2];
        }

        return $bloques;
    }

    /** ¿Tiene doble turno (segundo bloque completo)? */
    public static function esDobleTurno(?array $schedule): bool
    {
        return count(self::bloques($schedule)) > 1;
    }

    /** Minutos programados al día: suma de todos los bloques. */
    public static function minutosJornada(?array $schedule): int
    {
        $min = 0;
        foreach (self::bloques($schedule) as [$e, $s]) {
            $min += self::minutosEntre($e, $s);
        }

        return $min;
    }

    /** Hora de salida prevista del día (el último bloque). */
    public static function salidaFinal(?array $schedule): string
    {
        $bloques = self::bloques($schedule);
        if ($bloques === []) {
            return (string) ($schedule['hora_salida'] ?? '');
        }

        return $bloques[count($bloques) - 1][1];
    }

    /**
     * Minutos trabajados dentro de un tramo marcado, descontando el
     * descanso del doble turno.
     *
     * Ejemplo: 08:00 → 20:00 con bloques 08→13 y 16→20 = 12 h en el reloj
     * menos 3 h de descanso = 9 h reales.
     *
     * Si el horario tiene un solo turno, o el tramo cruza la medianoche,
     * devuelve la diferencia simple (el descanso no aplica).
     */
    public static function minutosTrabajados(string $desde, string $hasta, ?array $schedule): int
    {
        $total = self::minutosEntre($desde, $hasta);
        $bloques = self::bloques($schedule);

        if (count($bloques) < 2 || $total <= 0) {
            return $total;
        }

        [$e1, $s1] = $bloques[0];
        [$e2]      = $bloques[1];

        // Descanso entre bloques: sólo si está bien ordenado y no cruza
        // la medianoche (los turnos nocturnos no lo tocamos).
        $ini = strtotime('2000-01-01 ' . $desde);
        $fin = strtotime('2000-01-01 ' . $hasta);
        $brkIni = strtotime('2000-01-01 ' . $s1);
        $brkFin = strtotime('2000-01-01 ' . $e2);
        if ($ini === false || $fin === false || $brkIni === false || $brkFin === false) {
            return $total;
        }
        if ($fin < $ini || $brkFin <= $brkIni) {
            return $total;
        }

        $solape = min($fin, $brkFin) - max($ini, $brkIni);
        if ($solape > 0) {
            $total -= (int) round($solape / 60);
        }

        return max(0, $total);
    }

    /** Minutos entre dos horas 'HH:MM[:SS]' (0 si no se pueden leer). */
    public static function minutosEntre(string $desde, string $hasta): int
    {
        $a = strtotime('2000-01-01 ' . trim($desde));
        $b = strtotime('2000-01-01 ' . trim($hasta));
        if ($a === false || $b === false) {
            return 0;
        }

        $seg = $b - $a;
        if ($seg < 0) {
            $seg += 24 * 3600; // turno nocturno
        }

        return (int) round($seg / 60);
    }

    /** ¿La jornada de esa fecha ya terminó (para poder cerrar el día)? */
    public static function diaCerrado(?array $schedule, string $fecha, ?string $ahora = null): bool
    {
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) {
            return true;
        }
        if ($fecha > $hoy) {
            return false;
        }

        $salida = self::salidaFinal($schedule) ?: '17:00';
        $finDia = strtotime($salida) + 60 * 60; // 1 hora de gracia

        return strtotime($ahora ?? date('H:i:s')) > $finDia;
    }

    private static function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto);
        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
        ]);
        return $texto;
    }
}
