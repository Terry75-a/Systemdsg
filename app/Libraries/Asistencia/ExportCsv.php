<?php
namespace App\Libraries\Asistencia;

/**
 * Armado de archivos CSV del módulo de asistencia.
 * Separador ";" y BOM UTF-8 para que Excel abra las tildes bien.
 */
class ExportCsv
{
    private const SEPARADOR = ';';

    /** Estado interno => etiqueta que entiende el admin en el Excel. */
    private const ESTADOS = [
        'present' => 'Presente',
        'late'    => 'Tardanza',
        'absent'  => 'Falta',
        'no_exit' => 'Sin salida',
    ];

    public static function asistencias(array $filas, string $fi, string $ff): string
    {
        $salida = "\xEF\xBB\xBF";
        $salida .= implode(self::SEPARADOR, [
            'Fecha', 'Nombre', 'DNI', 'Entrada', 'Salida',
            'Estado', 'Observacion', 'Lat', 'Lng', 'IP',
        ]) . "\r\n";

        foreach ($filas as $f) {
            $salida .= self::fila([
                $f['date'] ?? '',
                $f['name'] ?? '',
                $f['dni'] ?? '',
                substr((string) ($f['time_in'] ?? ''), 0, 5),
                substr((string) ($f['time_out'] ?? ''), 0, 5),
                self::ESTADOS[(string) ($f['status'] ?? '')] ?? ($f['status'] ?? ''),
                $f['observacion'] ?? '',
                $f['lat'] ?? '',
                $f['lng'] ?? '',
                $f['ip'] ?? '',
            ]) . "\r\n";
        }

        return $salida;
    }

    /** Escapa un valor: sin saltos de línea ni ';' dentro del campo. */
    private static function fila(array $campos): string
    {
        return implode(self::SEPARADOR, array_map(
            static fn ($v) => preg_replace('/[\r\n;]+/', ' ', (string) $v),
            $campos
        ));
    }

    public static function nombreArchivo(string $fi, string $ff): string
    {
        return "asistencia_{$fi}_{$ff}.csv";
    }
}
