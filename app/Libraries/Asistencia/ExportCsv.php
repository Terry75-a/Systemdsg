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
        'absent_justified' => 'Falta justificada',
        'no_exit' => 'Sin salida',
    ];

    public static function asistencias(array $filas, string $fi, string $ff): string
    {
        $salida = "\xEF\xBB\xBF";
        $salida .= implode(self::SEPARADOR, [
            'Fecha', 'Nombre', 'DNI', '1a entrada', '1a salida', '2a entrada', '2a salida', 'Turnos',
            'Estado', 'Observacion', 'Lat', 'Lng', 'IP',
        ]) . "\r\n";

        foreach ($filas as $f) {
            $salida .= self::fila([
                $f['date'] ?? '',
                $f['name'] ?? '',
                $f['dni'] ?? '',
                // Doble turno: las 4 marcas. Un solo turno: entrada y salida en el 1er par.
                substr((string) ($f['time_in'] ?? ''), 0, 5),
                substr((string) (! empty($f['doble']) ? ($f['time_out1'] ?? '') : ($f['time_out'] ?? '')), 0, 5),
                ! empty($f['doble']) ? substr((string) ($f['time_in2'] ?? ''), 0, 5) : '',
                ! empty($f['doble']) ? substr((string) ($f['time_out'] ?? ''), 0, 5) : '',
                ! empty($f['doble']) ? 'Doble' : 'Uno',
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
