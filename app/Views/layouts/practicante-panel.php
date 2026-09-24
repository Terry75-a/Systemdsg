<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Mi Panel · DSG') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260923') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260923') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/practicante.css?v=20260924') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>
<div class="dev-layout">
    <?= view('partials/practicante-sidebar', ['activePage' => $activePage ?? 'dashboard']) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <?php $diasEs = ['', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo']; ?>
    <div class="dev-topbar-row">
                <div class="pra-greeting">
                    <h1>Hola, <strong><?= esc(session()->get('user_name') ?? 'Colaborador') ?></strong></h1>
                    <p><?= esc($greetingSub ?? 'Bienvenido a tu panel.') ?></p>
                    <div class="pra-page-meta">
                        <div class="pra-date">
                            <span class="material-symbols-outlined">calendar_today</span>
                            <?= esc(ucfirst($diasEs[(int) date('N')])) ?>, <?= date('d/m/Y') ?>
                        </div>
                        <div class="pra-clock-pill" id="praClockTrack">--:--</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>
            <?= $slot ?? '' ?>
        </div>
    </div>
</div>

<script>
(function() {
    var clock = document.getElementById('praClock');
    var clockSec = document.getElementById('praClockSec');
    var clockTrack = document.getElementById('praClockTrack');
    function tick() {
        var d = new Date();
        var hh = String(d.getHours()).padStart(2, '0');
        var mm = String(d.getMinutes()).padStart(2, '0');
        var ss = String(d.getSeconds()).padStart(2, '0');
        if (clock) clock.textContent = hh + ':' + mm;
        if (clockSec) clockSec.textContent = hh + ':' + mm + ':' + ss;
        if (clockTrack) clockTrack.textContent = hh + ':' + mm;
    }
    tick();
    setInterval(tick, 1000);
})();
</script>

</body>
</html>