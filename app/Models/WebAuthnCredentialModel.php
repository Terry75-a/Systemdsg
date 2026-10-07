<?php

namespace App\Models;

use CodeIgniter\Model;

class WebAuthnCredentialModel extends Model
{
    protected $table            = 'webauthn_credentials';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'user_id', 'credential_id', 'public_key', 'counter', 'user_handle',
        'aaguid', 'attestation_type', 'transports', 'backup_eligible',
        'backup_status', 'uv_initialized', 'created_at', 'last_used_at',
    ];

    public function findByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function findByCredentialId(string $credentialId): ?array
    {
        return $this->where('credential_id', $credentialId)->first();
    }

    public function clearUser(int $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
