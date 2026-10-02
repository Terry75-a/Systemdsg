<?php

namespace App\Libraries\Asistencia;

/**
 * Reglas de jornada compartidas por el panel de empleado, el cierre
 * automático y los comandos CLI del módulo de asistencia.
 */
class Jornada
{
    public const DIAS = [
        1 => 'lun', 2 => 'mar', 3 => 'mie', 4 => 'jue', 5 => 'vie', 6 => 'sab', 7 => 'dom',
    ];

    /** Horario aplicable a una persona: el asignado o el de su rol. */
    public static function elegir(array $schedules, int $scheduleId, string $role): array
    {
        $tipoRole = $role === 'Practicante' ? 'Practicante' : 'Empleado';

        if ($scheduleId > 0) {
            foreach ($schedules as $sch) {
                if ((int) ($sch['id'] ?? 0) === $scheduleId) {
                    return $sch;
                }
            }
        }

        foreach ($schedules as $sch) {
            if (($sch['tipo'] ?? '') === $tipoRole) {
                return $sch;
            }
        }

        return $schedules[0] ?? [];
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

        $salida = (string) ($schedule['hora_salida'] ?? '17:00');
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
