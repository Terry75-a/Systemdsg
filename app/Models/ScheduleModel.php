<?php
namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'nombre', 'tipo', 'dias', 'hora_entrada', 'hora_salida',
        'tolerancia', 'estado', 'qr_token', 'qr_updated',
    ];

    // ════════════════════════════════════════════════════════
    // QR DEL HORARIO (lo descarga el admin, lo escanea su personal)
    // ════════════════════════════════════════════════════════

    /**
     * Devuelve el token del QR del horario, creándolo si aún no existe.
     */
    public function ensureQrToken(int $scheduleId): string
    {
        $sch = $this->find($scheduleId);
        if (!$sch) {
            return '';
        }
        $token = (string) ($sch['qr_token'] ?? '');
        if ($token !== '') {
            return $token;
        }
        return $this->regenerateQrToken($scheduleId);
    }

    /**
     * Fuerza un token nuevo. El QR impreso anterior deja de servir.
     */
    public function regenerateQrToken(int $scheduleId): string
    {
        do {
            $token = bin2hex(random_bytes(20));
        } while ($this->where('qr_token', $token)->countAllResults() > 0);

        $this->update($scheduleId, [
            'qr_token'   => $token,
            'qr_updated' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function findByQrToken(string $token): ?array
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{40}$/', $token)) {
            return null;
        }
        return $this->where('qr_token', $token)->first();
    }

    public function getActive(?int $adminId = null, ?string $tipo = null): array
    {
        $builder = $this->where('estado', 'Activo');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        if ($tipo !== null && $tipo !== '') {
            $builder->where('tipo', $tipo);
        }
        return $builder->orderBy('id', 'DESC')->findAll();
    }

    public function getByTipo(int $adminId, string $tipo): array
    {
        return $this->where('admin_id', $adminId)
            ->where('tipo', $tipo)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function getAll(?int $adminId = null): array
    {
        $builder = $this->orderBy('id', 'DESC');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->findAll();
    }
}