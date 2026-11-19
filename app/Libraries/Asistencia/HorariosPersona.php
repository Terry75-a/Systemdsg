<?php

namespace App\Libraries\Asistencia;

/**
 * Horarios asignados a cada persona (tabla user_schedules).
 *
 * Una persona puede tener varios horarios, uno por grupo de días
 * («Lun - Vie» y «Sábado»). Para cada fecha se usa el horario cuyos
 * días la incluyen (ver Jornada::delDia).
 */
class HorariosPersona
{
    /** @var array<int, list<int>>|null user_id => [schedule_id, ...] */
    private static ?array $cache = null;

    public static function limpiarCache(): void
    {
        self::$cache = null;
    }

    /** Todas las asignaciones (una consulta por petición). */
    public static function mapa(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                $rows = db_connect()->table('user_schedules')->orderBy('schedule_id', 'ASC')->get()->getResultArray();
                foreach ($rows as $r) {
                    self::$cache[(int) $r['user_id']][] = (int) $r['schedule_id'];
                }
            } catch (\Throwable $e) {
                log_message('error', 'HorariosPersona: ' . $e->getMessage());
            }
        }
        return self::$cache;
    }

    /** Ids de horario de una persona; si no hay filas, su schedule_id. */
    public static function ids(array $user): array
    {
        $ids = self::mapa()[(int) ($user['id'] ?? 0)] ?? [];
        $principal = (int) ($user['schedule_id'] ?? 0);
        if ($ids === [] && $principal > 0) {
            $ids = [$principal];
        }
        return $ids;
    }

    /**
     * Deja a la persona con exactamente estos horarios y actualiza el
     * principal (users.schedule_id) al primero, o NULL si no queda ninguno.
     */
    public static function guardar(int $userId, array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        sort($ids);
        $db = db_connect();
        $db->table('user_schedules')->where('user_id', $userId)->delete();
        $ahora = date('Y-m-d H:i:s');
        foreach ($ids as $sid) {
            $db->table('user_schedules')->insert(['user_id' => $userId, 'schedule_id' => $sid, 'created_at' => $ahora]);
        }
        $db->table('users')->where('id', $userId)->update(['schedule_id' => $ids[0] ?? null]);
        self::limpiarCache();
    }
}
