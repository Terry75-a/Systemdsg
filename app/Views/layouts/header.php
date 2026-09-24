<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software especializado para restaurantes, boticas y minimarkets. Soluciones tecnologicas que impulsan tu negocio.">
    <title><?= esc($titulo ?? 'DSG PERU TECHNOLOGY') ?></title>

    <!-- Tipografias del sistema de asistencia -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Utilidades base del contenido -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/tabler-icons/tabler-icons.css') ?>">

    <!-- Landing -->
    <link rel="stylesheet" href="<?= base_url('css/index/components/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/stileprincipal.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/pricing.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/contactodemo.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dsg.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/servicios.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/asistencia.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login/cambiarcolor.css') ?>" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Diseno AsistDSG: panel -->
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard/panel-dsg.css?v=' . filemtime(FCPATH . 'css/dashboard/panel-dsg.css')) ?>">

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png')?>" >

    <style>
      body {
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
      }
      /* El contenedor heredado solo envuelve el panel; sin margenes del template */
      #main-wrapper { background: transparent; }
      #main-wrapper[data-layout="vertical"] { display: block; }
    </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">