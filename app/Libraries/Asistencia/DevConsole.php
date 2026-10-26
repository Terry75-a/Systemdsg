<?php
namespace App\Libraries\Asistencia;

/**
 * Datos de la Consola Dev (réplica de la página /dev de System-Trackin):
 * empresas, cuentas, actividad y estado de la base de datos de todo el sistema.
 */
class DevConsole
{
    /** Minutos desde el último ingreso para contar a alguien «en línea». */
    private const EN_LINEA_MIN = 60;

    public const ROLES = [
        'Dev'         => 'Desarrollador',
        'Admin'       => 'Administrador',
        'Empleado'    => 'Empleado',
        'Practicante' => 'Practicante',
    ];

    public const ESTADOS = [
        'present'          => 'Presente',
        'late'             => 'Tardanza',
        'absent'           => 'Falta',
        'absent_justified' => 'Falta justificada',
        'no_exit'          => 'Sin salida',
    ];

    public static function build(): array
    {
        $db  = db_connect();
        $hoy = date('Y-m-d');

        $users = $db->table('users')->select('id, name, role, admin_id, empresa, admin_code, estado, last_login, created')
            ->get()->getResultArray();

        // Último evento de sesión de cada usuario → «en línea»
        $ultimo = [];
        foreach ($db->query('SELECT s.user_id, s.evento, s.creado FROM session_log s
                JOIN (SELECT user_id, MAX(id) AS mx FROM session_log GROUP BY user_id) u ON u.mx = s.id')->getResultArray() as $r) {
            $ultimo[(int) $r['user_id']] = $r;
        }
        $limite = time() - self::EN_LINEA_MIN * 60;
        $enLinea = function (array $u) use ($ultimo, $limite): bool {
            $e = $ultimo[(int) $u['id']] ?? null;
            return $e && $e['evento'] === 'login' && strtotime($e['creado']) >= $limite;
        };

        $roles = array_fill_keys(array_keys(self::ROLES), 0);
        foreach ($users as $u) {
            if (isset($roles[$u['role']])) $roles[$u['role']]++;
        }

        // Empresas: un admin = una empresa, con su personal
        $empresas = [];
        foreach ($users as $u) {
            if ($u['role'] !== 'Admin') continue;
            $personal = array_filter($users, fn ($p) => (int) ($p['admin_id'] ?? 0) === (int) $u['id']
                && in_array($p['role'], ['Empleado', 'Practicante'], true));
            $empresas[] = [
                'name'      => trim((string) $u['empresa']) ?: $u['name'],
                'admin'     => $u['name'],
                'code'      => $u['admin_code'] ?? '',
                'estado'    => $u['estado'] ?? 'Activo',
                'created'   => substr((string) $u['created'], 0, 10),
                'last_seen' => $u['last_login'] ? substr((string) $u['last_login'], 0, 16) : null,
                'cuentas'   => count($personal),
                'practicantes' => count(array_filter($personal, fn ($p) => $p['role'] === 'Practicante')),
            ];
        }

        $empresaDe = [];
        foreach ($users as $u) {
            if ($u['role'] === 'Admin') $empresaDe[(int) $u['id']] = trim((string) $u['empresa']) ?: $u['name'];
        }

        // Últimas conexiones
        $conectados = array_values(array_filter($users, fn ($u) => !empty($u['last_login'])));
        usort($conectados, fn ($a, $b) => strcmp($b['last_login'], $a['last_login']));
        $recentUsers = array_map(fn ($u) => [
            'name'    => $u['name'],
            'role'    => self::ROLES[$u['role']] ?? $u['role'],
            'company' => $u['role'] === 'Admin' ? ($empresaDe[(int) $u['id']] ?? null) : ($empresaDe[(int) ($u['admin_id'] ?? 0)] ?? null),
            'last'    => substr((string) $u['last_login'], 0, 16),
            'online'  => $enLinea($u),
        ], array_slice($conectados, 0, 8));

        // Últimas marcaciones
        $events = array_map(fn ($r) => [
            'name'    => $r['name'] ?? '',
            'estado'  => self::ESTADOS[$r['status'] ?? ''] ?? ($r['status'] ?? ''),
            'status'  => $r['status'] ?? '',
            'jornada' => trim(substr((string) ($r['time_in'] ?? ''), 0, 5) . ($r['time_out'] ? ' → ' . substr((string) $r['time_out'], 0, 5) : '')),
            'company' => $empresaDe[(int) ($r['admin_id'] ?? 0)] ?? null,
            'date'    => $r['date'],
        ], $db->table('attendance')->orderBy('date', 'DESC')->orderBy('id', 'DESC')->limit(8)->get()->getResultArray());

        $marcaciones = (int) $db->table('attendance')->countAllResults();
        $marcHoy     = (int) $db->table('attendance')->where('date', $hoy)->countAllResults();
        $incPend     = (int) $db->table('incidents')->whereIn('estado', ['Pendiente', 'Revisión'])->countAllResults();
        $codigos     = (int) $db->table('codes')->where('status', 'active')->countAllResults();

        $schema = $db->query('SELECT COUNT(*) AS t, ROUND(SUM(data_length + index_length) / 1048576, 2) AS mb
            FROM information_schema.tables WHERE table_schema = DATABASE()')->getRowArray();

        return [
            'counts' => [
                'empresas'     => count($empresas),
                'cuentas'      => count($users),
                'online'       => count(array_filter($users, $enLinea)),
                'personal'     => $roles['Empleado'] + $roles['Practicante'],
                'marcaciones'  => $marcaciones,
                'marcHoy'      => $marcHoy,
                'incPend'      => $incPend,
                'codigos'      => $codigos,
            ],
            'roles'       => $roles,
            'empresas'    => $empresas,
            'recentUsers' => $recentUsers,
            'events'      => $events,
            'db'          => [
                'tables'  => (int) ($schema['t'] ?? 0),
                'sizeMb'  => (float) ($schema['mb'] ?? 0),
                'version' => (string) $db->getVersion(),
            ],
        ];
    }
}
