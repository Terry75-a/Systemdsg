<?php

use App\Libraries\Asistencia\AsistenciaQuery;
use App\Libraries\Asistencia\ExportCsv;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Lógica pura del módulo de asistencia: filtros de fecha y exportación CSV.
 *
 * @internal
 */
final class AsistenciaLibrariesTest extends CIUnitTestCase
{
    public function testRangoVacioUsaHoy(): void
    {
        [$fi, $ff] = AsistenciaQuery::rango([]);

        $this->assertSame(date('Y-m-d'), $fi);
        $this->assertSame($fi, $ff);
    }

    public function testRangoInvertidoSeCorrige(): void
    {
        [$fi, $ff] = AsistenciaQuery::rango([
            'fecha_inicio' => '2026-10-10',
            'fecha_fin'    => '2026-10-01',
        ]);

        $this->assertSame('2026-10-01', $fi);
        $this->assertSame('2026-10-10', $ff);
    }

    public function testRangoIgnoraEntradasNoTextuales(): void
    {
        // arrays / números en la query string no deben reventar (PHP 8)
        [$fi, $ff] = AsistenciaQuery::rango([
            'fecha_inicio' => ['2026-10-10'],
            'fecha_fin'    => 123,
        ]);

        $this->assertSame(date('Y-m-d'), $fi);
        $this->assertSame(date('Y-m-d'), $ff);
    }

    public function testCsvLlevaBOMYEncabezado(): void
    {
        $csv = ExportCsv::asistencias([], '2026-09-01', '2026-09-30');

        $this->assertSame("\xEF\xBB\xBF", substr($csv, 0, 3));
        $this->assertStringContainsString(
            'Fecha;Nombre;DNI;Entrada;Salida;Estado;Observacion;Lat;Lng;IP',
            $csv
        );
    }

    public function testCsvAplanaSaltosDeLineaYPuntosYComa(): void
    {
        $csv = ExportCsv::asistencias([
            [
                'date'        => '2026-09-30',
                'name'        => 'Ana Pérez',
                'dni'         => '12345678',
                'time_in'     => '08:00:00',
                'time_out'    => '17:00:00',
                'status'      => 'present',
                'observacion' => "Retraso;\njustificado",
                'lat'         => null,
                'lng'         => null,
                'ip'          => null,
            ],
        ], '2026-09-30', '2026-09-30');

        $lineas = explode("\r\n", rtrim($csv, "\r\n"));
        $this->assertCount(2, $lineas, 'Encabezado + 1 fila, sin filas rotas');

        $celdas = explode(';', $lineas[1]);
        $this->assertCount(10, $celdas);
        $this->assertSame('2026-09-30', $celdas[0]);
        $this->assertSame('Ana Pérez', $celdas[1]);
        $this->assertStringNotContainsString(';', $celdas[6]);
        $this->assertStringNotContainsString("\n", $celdas[6]);
    }

    public function testCsvTruncaHorasAAHoraMinuto(): void
    {
        $csv = ExportCsv::asistencias([
            ['date' => '2026-09-30', 'name' => 'Ana', 'dni' => '1', 'time_in' => '08:05:44', 'time_out' => '', 'status' => 'late', 'observacion' => ''],
        ], '2026-09-30', '2026-09-30');

        $this->assertStringContainsString('08:05;', $csv);
    }

    public function testNombreDeArchivo(): void
    {
        $this->assertSame(
            'asistencia_2026-09-01_2026-09-30.csv',
            ExportCsv::nombreArchivo('2026-09-01', '2026-09-30')
        );
    }

    public function testResumirCuentaTodoElRango(): void
    {
        $filas = [
            ['status' => 'present'],
            ['status' => 'present'],
            ['status' => 'late'],
            ['status' => 'absent'],
            ['status' => 'no_exit'],
            ['status' => 'desconocido'],
            [],
        ];

        $r = AsistenciaQuery::resumir($filas);

        $this->assertSame(7, $r['total']);
        $this->assertSame(2, $r['presentes']);
        $this->assertSame(1, $r['tardanzas']);
        $this->assertSame(1, $r['faltas']);
        $this->assertSame(1, $r['sin_salida']);
    }

    public function testResumirSinFilasDaCeros(): void
    {
        $this->assertSame(
            ['total' => 0, 'presentes' => 0, 'tardanzas' => 0, 'faltas' => 0, 'sin_salida' => 0],
            AsistenciaQuery::resumir([])
        );
    }

    public function testPorEstadoFiltraYVacioNoFiltra(): void
    {
        $filas = [
            ['status' => 'late'],
            ['status' => 'present'],
            ['status' => 'late'],
        ];

        $this->assertCount(2, AsistenciaQuery::porEstado($filas, 'late'));
        $this->assertCount(3, AsistenciaQuery::porEstado($filas, ''));
        $this->assertCount(3, AsistenciaQuery::porEstado($filas, 'todos'));
        $this->assertCount(0, AsistenciaQuery::porEstado($filas, 'absent'));
    }

    public function testCsvEtiquetaEstadosEnEspanol(): void
    {
        $csv = ExportCsv::asistencias([
            ['date' => '2026-09-30', 'name' => 'Ana', 'status' => 'no_exit'],
            ['date' => '2026-09-30', 'name' => 'Luis', 'status' => 'late'],
            ['date' => '2026-09-30', 'name' => 'María', 'status' => 'absent'],
        ], '2026-09-30', '2026-09-30');

        $this->assertStringContainsString(';Sin salida;', $csv);
        $this->assertStringContainsString(';Tardanza;', $csv);
        $this->assertStringContainsString(';Falta;', $csv);
        $this->assertStringNotContainsString('no_exit', $csv);
    }
}
