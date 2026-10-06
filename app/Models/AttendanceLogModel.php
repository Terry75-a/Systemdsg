<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceLogModel extends Model
{
    protected $table            = 'attendance_log';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'attendance_id', 'admin_id', 'user_id', 'accion', 'campo',
        'valor_anterior', 'valor_nuevo', 'motivo', 'autor', 'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Registra un cambio sobre una marcación. Nunca lanza excepción:
     * la auditoría no debe romper la operación principal.
     */
    public function registrar(array $data): bool
    {
        try {
            $this->insert($data, false);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'No se pudo auditar asistencia: {msg}', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function deAsistencia(int $attId, int $limite = 50): array
    {
        return $this->where('attendance_id', $attId)
            ->orderBy('id', 'DESC')
            ->limit($limite)
            ->findAll();
    }

    public function deAdmin(int $adminId, int $limite = 100): array
    {
        return $this->where('admin_id', $adminId)
            ->orderBy('id', 'DESC')
            ->limit($limite)
            ->findAll();
    }
}
