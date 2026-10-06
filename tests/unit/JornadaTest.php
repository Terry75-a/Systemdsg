<?php

use App\Libraries\Asistencia\Jornada;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Reglas de jornada compartidas por el panel, el cierre automático
 * y los comandos CLI del módulo de asistencia.
 *
 * @internal
 */
final class JornadaTest extends CIUnitTestCase
{
    private array $horarios = [
        ['id' => 1, 'tipo' => 'Empleado',     'dias' => 'Lun - Vie', 'hora_entrada' => '08:00', 'hora_salida' => '17:00', 'tolerancia' => 10],
        ['id' => 2, 'tipo' => 'Practicante',  'dias' => 'Lun - Vie', 'hora_entrada' => '07:00', 'hora_salida' => '12:00', 'tolerancia' => 5],
    ];

    public function testSinHorarioSeAsumeLunesAViernes(): void
    {
        $this->assertTrue(Jornada::esDiaLaborable(null, '2026-09-28'));  // lunes
        $this->assertTrue(Jornada::esDiaLaborable(null, '2026-09-30'));  // miércoles
        $this->assertTrue(Jornada::esDiaLaborable(null, '2026-10-02'));  // viernes
        $this->assertFalse(Jornada::esDiaLaborable(null, '2026-10-03')); // sábado
        $this->assertFalse(Jornada::esDiaLaborable(null, '2026-09-27')); // domingo
    }

    public function testDomingoNuncaEsLaborable(): void
    {
        $this->assertFalse(Jornada::esDiaLaborable(['dias' => '1-7'], '2026-09-27'));
        $this->assertFalse(Jornada::esDiaLaborable(['dias' => 'Dom'], '2026-09-27'));
    }

    public function testHorarioConDiasEnTexto(): void
    {
        $this->assertTrue(Jornada::esDiaLaborable(['dias' => 'Lun - Vie'], '2026-09-30'));
        $this->assertFalse(Jornada::esDiaLaborable(['dias' => 'Lun - Vie'], '2026-10-03'));

        $sabado = '2026-10-03';
        $this->assertTrue(Jornada::esDiaLaborable(['dias' => 'Lun - Sáb'], $sabado));
        $this->assertFalse(Jornada::esDiaLaborable(['dias' => 'Sáb'], '2026-09-30'));
    }

    public function testHorarioConDiasNumericos(): void
    {
        // 1 = lunes … 7 = domingo
        $this->assertTrue(Jornada::esDiaLaborable(['dias' => '1-6'], '2026-10-03'));  // sábado
        $this->assertFalse(Jornada::esDiaLaborable(['dias' => '1-5'], '2026-10-03')); // sábado
        $this->assertTrue(Jornada::esDiaLaborable(['dias' => '1,3,5'], '2026-09-30')); // miércoles = 3
    }

    public function testElHorarioAsignadoTienePrioridadSobreElDelRol(): void
    {
        $elegido = Jornada::elegir($this->horarios, 2, 'Empleado');
        $this->assertSame(2, (int) $elegido['id']);

        $elegido = Jornada::elegir($this->horarios, 0, 'Practicante');
        $this->assertSame(2, (int) $elegido['id']);

        $elegido = Jornada::elegir($this->horarios, 0, 'Empleado');
        $this->assertSame(1, (int) $elegido['id']);
    }

    public function testSinCoincidenciaDevuelveElPrimeroOVacio(): void
    {
        $this->assertSame([], Jornada::elegir([], 0, 'Empleado'));
        $this->assertSame(1, (int) Jornada::elegir($this->horarios, 99, 'Dev')['id']);
    }

    public function testLimiteDeEntradaSumaLaTolerancia(): void
    {
        $limite = Jornada::limiteEntrada($this->horarios[0]);
        $this->assertSame('08:10', date('H:i', $limite));

        $limite = Jornada::limiteEntrada($this->horarios[1]);
        $this->assertSame('07:05', date('H:i', $limite));
    }

    public function testElDiaSoloSeCierraCuandoTerminaLaJornada(): void
    {
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $manana = date('Y-m-d', strtotime('+1 day'));

        $this->assertTrue(Jornada::diaCerrado(null, $ayer));
        $this->assertFalse(Jornada::diaCerrado(null, $manana));

        // Hoy, antes de la salida + 1 h de gracia, sigue abierto
        $this->assertFalse(Jornada::diaCerrado(null, date('Y-m-d'), '09:00:00'));
        $this->assertTrue(Jornada::diaCerrado(null, date('Y-m-d'), '18:30:00'));
    }
}
