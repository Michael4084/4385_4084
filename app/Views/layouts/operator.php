<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Opérateur - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    
 
</head>
<body>

<!-- Header -->
<header class="header-operator">
    <div class="d-flex align-items-center gap-3">
        <button class="menu-toggle d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <a href="#" class="brand">
            <i class="bi bi-wallet2"></i>
            <span class="d-none d-sm-inline">Opérateur Mobile Money</span>
            <span class="d-inline d-sm-none">Opérateur</span>
        </a>
    </div>
    <div class="header-actions">
        <a class="nav-link-operator" href="<?= site_url('operator/dashboard') ?>">
            <i class="bi bi-speedometer2 me-1"></i>
            <span class="d-none d-sm-inline">Dashboard</span>
        </a>
        <a class="nav-link-operator" href="<?= site_url('operator/commissions') ?>">
            <i class="bi bi-percent me-1"></i>
            <span class="d-none d-sm-inline">Commissions</span>
        </a>
        <a class="nav-link-operator" href="<?= site_url('operator/logout') ?>">
            <i class="bi bi-box-arrow-right me-1"></i>
            <span class="d-none d-sm-inline">Déconnexion</span>
        </a>
    </div>
</header>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar-operator" id="sidebarMenu">
    <div class="px-3 mb-3 d-none d-md-block">
        <small class="text-muted text-uppercase fw-bold">Navigation</small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link-operator <?= url_is('operator/dashboard') ? 'active' : '' ?>" href="<?= site_url('operator/dashboard') ?>">
                <i class="bi bi-house-door"></i> Vue d'ensemble
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-operator <?= url_is('operator/prefixes*') ? 'active' : '' ?>" href="<?= site_url('operator/prefixes') ?>">
                <i class="bi bi-hash"></i> Préfixes Téléphoniques
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-operator <?= url_is('operator/operation-types*') ? 'active' : '' ?>" href="<?= site_url('operator/operation-types') ?>">
                <i class="bi bi-gear"></i> Types d'opérations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-operator <?= url_is('operator/fees*') ? 'active' : '' ?>" href="<?= site_url('operator/fees') ?>">
                <i class="bi bi-currency-exchange"></i> Barèmes de frais
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link-operator <?= url_is('operator/clients*') ? 'active' : '' ?>" href="<?= site_url('operator/clients') ?>">
                <i class="bi bi-people"></i> Comptes Clients
            </a>
        </li>
    </ul>
</nav>

<!-- Contenu principal -->
<main class="main-operator">
    <div class="content-card">
        <!-- Messages flash -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-operator alert-operator-success mb-4">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span><?= session()->getFlashdata('success') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-operator alert-operator-danger mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span><?= session()->getFlashdata('error') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Contenu de la page -->
        <?= $this->renderSection('content') ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }
</script>
</body>
</html>