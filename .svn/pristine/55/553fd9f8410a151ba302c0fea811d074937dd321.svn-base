<?php
namespace App\Models;

use CodeIgniter\Model;

class ClientesModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $returnType = 'object';
    protected $allowedFields = ['id_cliente', 'id_persona', 'id_empresa', 'created_at', 'estado'];
    public $useTimestamps = false;

    public function obtenerClientesConDatos()
    {
        $clientes = $this->select('clientes.*, personas.nombre, personas.apellido_paterno, personas.apellido_materno, personas.dni, personas.correo, personas.telefono, empresa.razon_social, empresa.ruc, empresa.id_empresa')
            ->join('personas', 'personas.id_persona = clientes.id_persona')
            ->join('empresa', 'empresa.id_empresa = clientes.id_empresa', 'left')
            ->orderBy('clientes.id_cliente', 'DESC')
            ->findAll();

        return array_map(fn (object $cliente) => $this->mapearClienteListado($cliente), $clientes);
    }

    public function obtenerClientePorId(int $id): ?object
    {
        return $this->select('clientes.*, personas.nombre, personas.apellido_paterno, personas.apellido_materno, personas.dni, personas.correo, personas.telefono, personas.direccion, personas.fecha_nacimiento, personas.id_distrito, empresa.id_empresa, empresa.ruc, empresa.razon_social, empresa.direccion AS direccion_empresa, empresa.telefono AS telefono_empresa, empresa.correo AS correo_empresa, empresa.id_departamento, empresa.id_provincia, empresa.id_distrito AS id_distrito_empresa')
            ->join('personas', 'personas.id_persona = clientes.id_persona')
            ->join('empresa', 'empresa.id_empresa = clientes.id_empresa', 'left')
            ->where('clientes.id_cliente', $id)
            ->first();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function obtenerDetalleClientePorId(int $id): ?array
    {
        $cliente = $this->obtenerClientePorId($id);

        if ($cliente === null) {
            return null;
        }

        $idEmpresa = isset($cliente->id_empresa) ? (int) $cliente->id_empresa : null;

        return [
            'cliente' => [
                'id_cliente' => (int) $cliente->id_cliente,
                'estado' => (int) ($cliente->estado ?? 0),
                'created_at' => $cliente->created_at,
            ],
            'persona' => [
                'id_persona' => (int) ($cliente->id_persona ?? $cliente->id_cliente),
                'nombre' => (string) ($cliente->nombre ?? ''),
                'apellido_paterno' => (string) ($cliente->apellido_paterno ?? ''),
                'apellido_materno' => $cliente->apellido_materno,
                'nombre_completo' => $this->construirNombreCompleto($cliente),
                'dni' => (string) ($cliente->dni ?? ''),
                'correo' => (string) ($cliente->correo ?? ''),
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'fecha_nacimiento' => $cliente->fecha_nacimiento,
                'id_distrito' => $cliente->id_distrito,
            ],
            'empresa' => $idEmpresa === null ? null : [
                'id_empresa' => $idEmpresa,
                'ruc' => (string) ($cliente->ruc ?? ''),
                'razon_social' => (string) ($cliente->razon_social ?? ''),
                'direccion' => $cliente->direccion_empresa,
                'telefono' => $cliente->telefono_empresa,
                'correo' => $cliente->correo_empresa,
                'id_departamento' => $cliente->id_departamento,
                'id_provincia' => $cliente->id_provincia,
                'id_distrito' => $cliente->id_distrito_empresa,
            ],
        ];
    }

    public function actualizarEstadoPorId(int $id, int $estado): bool
    {
        return $this->update($id, ['estado' => $estado]);
    }

    private function mapearClienteListado(object $cliente): object
    {
        $cliente->nombre_completo = $this->construirNombreCompleto($cliente);
        $cliente->telefono = $cliente->telefono ?: '—';
        $cliente->razon_social = $cliente->razon_social ?: 'Sin empresa asociada';
        $cliente->ruc = $cliente->ruc ?: '—';
        $cliente->estado_label = ((int) ($cliente->estado ?? 1)) === 1 ? 'Activo' : 'Inactivo';
        $cliente->estado_badge = ((int) ($cliente->estado ?? 1)) === 1 ? 'success' : 'secondary';

        return $cliente;
    }

    private function construirNombreCompleto(object $cliente): string
    {
        $partes = array_filter([
            trim((string) ($cliente->nombre ?? '')),
            trim((string) ($cliente->apellido_paterno ?? '')),
            trim((string) ($cliente->apellido_materno ?? '')),
        ], static fn (string $parte): bool => $parte !== '');

        return implode(' ', $partes);
    }


}
