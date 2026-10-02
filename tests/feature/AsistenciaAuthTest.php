<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Barrera de acceso del módulo de asistencia:
 *  /admin/*  y  /mi-panel/*  piden sesión y rol.
 *
 * @internal
 */
final class AsistenciaAuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLoginDelModuloEstaDisponible(): void
    {
        $r = $this->get('login-verde');
        $r->assertStatus(200);
        $r->assertSee('login-form');
    }

    public function testAdminSinSesionSeEnviaAlLoginDelModulo(): void
    {
        foreach (['admin', 'admin/personal', 'admin/asistencias', 'admin/configuracion'] as $ruta) {
            $r = $this->get($ruta);
            $r->assertStatus(302);
            $this->assertStringContainsString('login-verde', (string) $r->getRedirectUrl(), "Fallo en {$ruta}");
        }
    }

    public function testMiPanelSinSesionSeEnviaAlLoginDelModulo(): void
    {
        foreach (['mi-panel', 'mi-panel/asistencias', 'mi-panel/horario', 'mi-panel/incidencias'] as $ruta) {
            $r = $this->get($ruta);
            $r->assertStatus(302);
            $this->assertStringContainsString('login-verde', (string) $r->getRedirectUrl(), "Fallo en {$ruta}");
        }
    }

    /**
     * Un POST al panel sin sesión nunca debe llegar a ejecutar la acción:
     * lo frena el CSRF (global) y, si pasara, el filtro `asistencia`.
     */
    public function testPostProtegidoNoPasaSinSesion(): void
    {
        try {
            $r      = $this->post('admin/save-config', ['cfg_empresa' => 'Hackeo']);
            $codigo = $r->response()->getStatusCode();
        } catch (\CodeIgniter\Security\Exceptions\SecurityException $e) {
            $this->assertTrue(true, 'CSRF frenó la petición');

            return;
        }

        $this->assertNotSame(200, $codigo);
        $this->assertNotSame(303, $codigo);
        $this->assertStringContainsString('login-verde', (string) $r->getRedirectUrl());
    }

    public function testRutasDelSistemaDelCompaneroSiguenIguias(): void
    {
        $this->get('login')->assertStatus(200);
        $this->get('dashboard')->assertRedirect();
    }
}
