<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'user_id', 'tipo', 'titulo', 'mensaje', 'datos', 'leida', 'leida_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $useSoftDeletes = false;

    // ══════════════════════ ADMIN ══════════════════════

    public function getUnreadCount(int $adminId): int
    {
        return $this->where('admin_id', $adminId)
            ->where('user_id', null)
            ->where('leida', 0)
            ->countAllResults();
    }

    public function getForAdmin(int $adminId, int $limit = 50, int $offset = 0): array
    {
        return $this->where('admin_id', $adminId)
            ->where('user_id', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    public function markAsRead(int $notificationId, int $adminId): bool
    {
        return $this->where('id', $notificationId)
            ->where('admin_id', $adminId)
            ->where('user_id', null)
            ->update(null, ['leida' => 1, 'leida_at' => date('Y-m-d H:i:s')]);
    }

    public function markAllAsRead(int $adminId): bool
    {
        $this->where('admin_id', $adminId)->where('user_id', null)->where('leida', 0);
        return $this->update(null, ['leida' => 1, 'leida_at' => date('Y-m-d H:i:s')]);
    }

    // ══════════════════════ EMPLEADO ══════════════════════

    public function getUnreadCountForUser(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('leida', 0)
            ->countAllResults();
    }

    public function getForUser(int $userId, int $limit = 50, int $offset = 0): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    public function markAsReadForUser(int $notificationId, int $userId): bool
    {
        return $this->where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(null, ['leida' => 1, 'leida_at' => date('Y-m-d H:i:s')]);
    }

    public function markAllAsReadForUser(int $userId): bool
    {
        $this->where('user_id', $userId)->where('leida', 0);
        return $this->update(null, ['leida' => 1, 'leida_at' => date('Y-m-d H:i:s')]);
    }
}
