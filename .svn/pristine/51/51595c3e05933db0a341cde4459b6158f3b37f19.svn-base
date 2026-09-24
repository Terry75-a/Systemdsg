<?php

namespace App\Controllers;

use App\Services\External\DocumentoLookupService;

class BusquedaController extends BaseController
{
    protected DocumentoLookupService $documentoLookupService;

    public function __construct()
    {
        $this->documentoLookupService = service('documentoLookup');
    }

    public function obtenerDni(string $dni): array
    {
        return $this->documentoLookupService->buscarDni($dni)->toArray();
    }

    public function obtenerRuc(string $ruc): array
    {
        return $this->documentoLookupService->buscarRuc($ruc)->toArray();
    }
}
