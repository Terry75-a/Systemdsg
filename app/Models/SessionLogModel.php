<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionLogModel extends Model
{
    protected $table         = 'session_log';
    protected $allowedFields = ['user_id', 'name', 'role', 'evento', 'ip', 'creado'];
    protected $useTimestamps = true;
    protected $createdField  = 'creado';
    protected $updatedField  = 'creado'; // la tabla no tiene updated_at
    protected $dateFormat    = 'datetime';

    /** Registra entrada/salida sin romper el flujo de login/logout si falla. */
    public function registrar(?int $userId, string $name, string $role, string $evento, string $ip): void
    {
        try {
            $this->insert([
                'user_id' => $userId,
                'name'    => mb_substr($name, 0, 120),
                'role'    => mb_substr($role, 0, 40),
                'evento'  => $evento,
                'ip'      => $ip,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'session_log registrar: ' . $e->getMessage());
        }
    }

    /** Totales globales: entradas, salidas y usuarios distintos. */
    public function contarEventos(): array
    {
        $out = ['login' => 0, 'logout' => 0, 'usuarios' => 0];
        foreach ($this->select('evento, COUNT(*) AS c')->groupBy('evento')->findAll() as $row) {
            $out[$row['evento']] = (int) $row['c'];
        }
        $row = $this->db->query('SELECT COUNT(DISTINCT name) AS c FROM ' . $this->table)->getRow();
        $out['usuarios'] = (int) ($row->c ?? 0);
        return $out;
    }

    /** Historial con filtros opcionales: evento y texto (nombre o IP). */
    public function filtrar(string $evento = '', string $q = '', int $limite = 300): array
    {
        if ($evento === 'login' || $evento === 'logout') {
            $this->where('evento', $evento);
        }
        if ($q !== '') {
            $this->groupStart()->like('name', $q)->orLike('ip', $q)->groupEnd();
        }
        return $this->orderBy('id', 'DESC')->limit($limite)->findAll();
    }
}
