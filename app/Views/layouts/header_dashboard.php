<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'DSG LOGIN') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Vendors -->
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/tabler-icons/tabler-icons.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?= base_url('css/login/loginindex.css?v=' . filemtime(FCPATH . 'css/login/loginindex.css')) ?>" />
    <link rel="stylesheet" href="<?= base_url('css/login/cambiarcolor.css') ?>" />
    <!-- Custom Dashboard Styles -->
    <link rel="stylesheet" href="<?= base_url('css/dashboard/dashboard.css') ?>">
    
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png')?>" > 

    <style>
      /* --- CONTENEDOR CAMPANA --- */
.dsg-notification-wrap {
    position: relative !important;
    display: inline-flex !important;
    align-items: center;
}

/* --- BADGE ROJO ESTILO YOUTUBE --- */
.dsg-notif-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background-color: #cc0000;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    min-width: 17px;
    height: 17px;
    line-height: 17px;
    border-radius: 50%;
    text-align: center;
    border: 2px solid #ffffff;
    pointer-events: none;
}

/* --- PANEL FLOTANTE ESTILO YOUTUBE --- */
.dsg-notif-dropdown {
    position: absolute !important;
    top: calc(100% + 10px) !important;
    right: 0 !important;
    width: 380px !important;
    max-width: 90vw;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.18);
    border: 1px solid rgba(0, 0, 0, 0.08);
    z-index: 99999 !important;
    overflow: hidden;
    animation: ytFadeIn .18s cubic-bezier(0.2, 0.9, 0.3, 1);
}

@keyframes ytFadeIn {
    from { opacity: 0; transform: translateY(-8px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.dsg-notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 15px;
    font-weight: 600;
    color: #0f0f0f;
}

.dsg-notif-list {
    max-height: 380px;
    overflow-y: auto;
}

/* Scrollbar fina */
.dsg-notif-list::-webkit-scrollbar {
    width: 6px;
}
.dsg-notif-list::-webkit-scrollbar-thumb {
    background: #cccccc;
    border-radius: 10px;
}

/* ITEM ESTILO YOUTUBE */
.dsg-notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: inherit;
    position: relative;
    transition: background 0.15s ease;
}
.dsg-notif-item:hover {
    background-color: #f2f2f2;
}

/* Punto azul de no leído */
.dsg-notif-dot {
    width: 6px;
    height: 6px;
    background: #065fd4;
    border-radius: 50%;
    margin-top: 14px;
    flex-shrink: 0;
}

/* Avatar circular del cliente o alerta */
.dsg-notif-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #0f0f0f;
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dsg-notif-content {
    flex: 1;
    min-width: 0;
}

.dsg-notif-title {
    font-size: 13px;
    line-height: 1.35;
    font-weight: 500;
    color: #0f0f0f;
    margin-bottom: 3px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.dsg-notif-time {
    font-size: 11px;
    color: #606060;
}
    </style>
</head>
<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">