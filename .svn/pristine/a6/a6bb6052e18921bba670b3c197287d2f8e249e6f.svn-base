<?php

namespace App\Controllers;

use App\Services\Shared\BusinessException;
use App\Services\Shared\ServiceResult;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    protected function verificarSesionActiva(): mixed
    {
        if (!esta_logueado()) {
            return respuesta_auth_requerida();
        }

        return null;
    }

    protected function requerirAdminBackend(string $message = 'Solo un administrador puede acceder a este módulo.'): mixed
    {
        $redir = $this->verificarSesionActiva();
        if ($redir) {
            return $redir;
        }

        if (!es_admin()) {
            return respuesta_auth_denegada($message);
        }

        return null;
    }

    protected function esperaJson(): bool
    {
        return $this->request->isAJAX()
            || str_contains(strtolower($this->request->getHeaderLine('Accept')), 'application/json');
    }

    protected function crearDatosVistaAdmin(string $titulo, ?string $lastPage = null, array $extra = []): array
    {
        if ($lastPage !== null) {
            session()->set('last_page', $lastPage);
        }

        return array_merge([
            'titulo' => $titulo,
            'username' => session()->get('username'),
        ], $extra);
    }

    protected function renderAdminPage(string $view, array $data = []): string
    {
        return view('layouts/header', $data)
            . view('layouts/sidebar')
            . view('layouts/topbar', $data)
            . view($view, $data)
            . view('layouts/footer');
    }

    protected function responderJson(bool $success, string $message, int $status = 200, array $extra = []): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'success' => $success,
                'message' => $message,
            ] + $extra);
    }

    protected function responderValidacion(array $errors, string $message = 'Hay errores de validación.'): ResponseInterface
    {
        return $this->responderJson(false, $message, 422, ['errors' => $errors]);
    }

    protected function responderResultadoServicio(ServiceResult $result): ResponseInterface
    {
        return $this->response
            ->setStatusCode($result->getStatus())
            ->setJSON($result->toArray());
    }

    protected function ejecutarAccionDeServicio(callable $action, string $publicMessage, string $logContext): ResponseInterface
    {
        try {
            $result = $action();

            return $this->responderResultadoServicio($result);
        } catch (BusinessException $exception) {
            return $this->responderErrorNegocio($exception);
        } catch (\Throwable $exception) {
            return $this->responderExcepcion($exception, $publicMessage, $logContext);
        }
    }

    protected function responderErrorNegocio(BusinessException $exception): ResponseInterface
    {
        return $this->responderResultadoServicio($exception->toServiceResult());
    }

    protected function responderNoEncontrado(string $message = 'Registro no encontrado.'): ResponseInterface
    {
        return $this->responderJson(false, $message, 404);
    }

    protected function responderExcepcion(
        \Throwable $exception,
        string $publicMessage = 'Ocurrió un error interno al procesar la solicitud.',
        string $logContext = 'controller_error'
    ): ResponseInterface {
        log_message(
            'error',
            '[{context}] {exception}: {message} in {file}:{line}',
            [
                'context' => $logContext,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]
        );

        return $this->responderJson(false, $publicMessage, 500);
    }

    protected function redirigirConFlash(string $route, string $message, string $type = 'success')
    {
        session()->setFlashdata('msg', $message);
        session()->setFlashdata('tipo', $type);

        return redirect()->to(base_url($route));
    }
}
