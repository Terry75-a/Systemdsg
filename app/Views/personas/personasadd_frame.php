<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar / Editar Persona</title>

    <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/libs/bootstrap/dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard/panel-dsg.css?v=' . filemtime(FCPATH . 'css/dashboard/panel-dsg.css')) ?>">

    <style>
        body {
            background: #fff;
            padding: 0 0 12px;
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        html.dark body { background: #1f1f1f; }
        .body-wrapper { padding: 0 !important; }
        .pdsg-add-wrap.card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important; }
    </style>
</head>

<body class="dev-main pe-frame">
    <div class="body-wrapper">
        <?= view('personas/personasadd', [
            'persona'       => $persona ?? null,
            'empresa'       => $empresa ?? null,
            'sucursales'    => $sucursales ?? [],
            'departamentos' => $departamentos ?? []
        ]) ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
        // Propagar el modo oscuro del padre hacia el iframe
        (function () {
            try {
                var padre = window.parent.document;
                if (padre && padre.documentElement && padre.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) { /* misma origin: no debería fallar */ }
        })();
    </script>
</body>
</html>