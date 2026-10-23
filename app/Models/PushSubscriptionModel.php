<?php

namespace App\Models;

use CodeIgniter\Model;

class PushSubscriptionModel extends Model
{
    protected $table            = 'push_subscriptions';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'endpoint', 'p256dh', 'auth', 'content_coding', 'last_seen'];
    protected $useTimestamps    = false;
    protected $returnType       = 'array';

    public function upsertParaUsuario(int $userId, string $endpoint, string $p256dh, string $auth): void
    {
        $existente = $this->where('endpoint', $endpoint)->first();
        if ($existente) {
            $this->update($existente['id'], [
                'user_id'   => $userId,
                'p256dh'    => $p256dh,
                'auth'      => $auth,
                'last_seen' => date('Y-m-d H:i:s'),
            ]);
            return;
        }
        $this->insert([
            'user_id'    => $userId,
            'endpoint'   => $endpoint,
            'p256dh'     => $p256dh,
            'auth'       => $auth,
            'last_seen'  => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function quitar(string $endpoint, int $userId): void
    {
        $this->where('endpoint', $endpoint)->where('user_id', $userId)->delete();
    }

    public function deUsuarios(array $userIds)
    {
        return $this->whereIn('user_id', $userIds)->findAll();
    }
}
