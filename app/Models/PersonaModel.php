<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table            = 'personas';
    protected $primaryKey       = 'id_persona';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'telefono',
        'correo',
        'direccion',
        'fecha_nacimiento',
        'id_pais',
        'id_departamento',
        'id_provincia',
        'id_tipo_documento',
        'id_distrito',
        'imagen',
        'created_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Verifica si el DNI ya existe (excluyendo opcionalmente un id)
    public function dniExiste(string $dni, ?int $excluirId = null): bool
    {
        $builder = $this->where('dni', $dni);
        if ($excluirId !== null) {
            $builder->where('id_persona !=', $excluirId);
        }
        return $builder->countAllResults() > 0;
    }
}