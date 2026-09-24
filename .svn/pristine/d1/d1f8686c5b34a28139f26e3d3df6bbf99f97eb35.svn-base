<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Format\JSONFormatter;
use CodeIgniter\Format\XMLFormatter;

class Format extends BaseConfig
{
    // ══════════════════════════════════════════════════════════════════
    // Tipos de respuesta soportados por $response->setJSON() y setXML()
    // CI4 usa este archivo para saber cómo formatear las respuestas API
    // ══════════════════════════════════════════════════════════════════

    public array $supportedResponseFormats = [
        'application/json',
        'application/xml',
        'text/xml',
    ];

    // ══════════════════════════════════════════════════════════════════
    // Formateadores disponibles por tipo MIME
    // ══════════════════════════════════════════════════════════════════
    public array $formatters = [
        'application/json' => JSONFormatter::class ,
        'application/xml' => XMLFormatter::class ,
        'text/xml' => XMLFormatter::class ,
    ];

    // ══════════════════════════════════════════════════════════════════
    // Opciones para el formateador JSON
    // JSON_UNESCAPED_UNICODE → muestra ñ, á, é en vez de \u00f1
    // JSON_UNESCAPED_SLASHES → no escapa las barras /
    // JSON_PRETTY_PRINT      → formato legible en desarrollo (quítalo en producción)
    // ══════════════════════════════════════════════════════════════════
    public array $formatterOptions = [
        'application/json' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        'application/xml' => 0,
        'text/xml' => 0,
    ];
}
