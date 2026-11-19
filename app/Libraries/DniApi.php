<?php

namespace App\Libraries;

/**
 * Consulta de DNI con la API de DSG Perú (la misma que usa el panel de
 * clientes en PersonasAddController::buscarDni).
 *
 * El token puede ir en .env como `dsg.apiToken = ...`; si no está, se usa
 * el mismo que ya tenía el panel de clientes. Las respuestas se guardan en
 * caché un día para no repetir consultas del mismo DNI.
 */
class DniApi
{
    private const URL = 'https://www.dsgperutech.com/api-customer/v1/dni/';
    private const TOKEN_POR_DEFECTO = '3d524a53c110e4c22463b10ed32cef9d';

    /**
     * @return array{nombres:string, paterno:string, materno:string, completo:string}|null
     *         null si el DNI no existe o el servicio no respondió.
     */
    public static function consultar(string $dni): ?array
    {
        if (! preg_match('/^\d{8}$/', $dni)) {
            return null;
        }
        $clave = 'dniapi_' . $dni;
        if (($guardado = cache($clave)) !== null) {
            return $guardado ?: null;
        }

        try {
            $resp = \Config\Services::curlrequest()->get(self::URL . $dni, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => (string) (env('dsg.apiToken') ?: self::TOKEN_POR_DEFECTO),
                ],
                'http_errors'     => false,
                'timeout'         => 15,
                'connect_timeout' => 6,
                'allow_redirects' => true,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'DniApi: ' . $e->getMessage());
            return null; // sin caché: puede ser un corte momentáneo
        }

        $data = json_decode((string) $resp->getBody());
        if ($resp->getStatusCode() !== 200 || ! $data || empty($data->success) || empty($data->result)) {
            cache()->save($clave, [], 3600); // "no encontrado" se recuerda 1 h
            return null;
        }

        $r = $data->result;
        $nombres = self::limpiar($r->name ?? '');
        $paterno = self::limpiar($r->paternal ?? '');
        $materno = self::limpiar($r->maternal ?? '');
        $out = [
            'nombres'  => $nombres,
            'paterno'  => $paterno,
            'materno'  => $materno,
            'completo' => trim($nombres . ' ' . $paterno . ' ' . $materno),
        ];
        cache()->save($clave, $out, 86400);
        return $out;
    }

    /** "JUAN CARLOS" → "Juan Carlos" (respeta tildes y ñ). */
    private static function limpiar(string $s): string
    {
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        return $s === '' ? '' : mb_convert_case(mb_strtolower($s, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
