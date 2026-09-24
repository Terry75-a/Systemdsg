<?php
namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $table            = 'admin_config';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'empresa', 'ruc', 'direccion', 'email', 'telefono',
        'horario_default', 'tolerancia_default', 'notif_email', 'notif_email_dest',
    ];

    public function getConfig(?int $adminId = null): array
    {
        $builder = $this->where('admin_id', $adminId);
        $config  = $builder->first();

        if (!$config) {
            $this->insert(['admin_id' => $adminId, 'empresa' => 'DSG PERU TECHNOLOGY SAC']);
            $builder = $this->where('admin_id', $adminId);
            $config  = $builder->first();
        }
        return $config ?? [];
    }

    public function saveConfig(array $data, ?int $adminId = null): bool
    {
        $existing = $this->where('admin_id', $adminId)->first();
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        return (bool) $this->insert(array_merge($data, ['admin_id' => $adminId]));
    }
}