<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mobile Money' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    
</head>
<body class="app-shell">

<?php if(session()->get('client_id')): ?>
<header class="top-nav">
    <div class="app-wrapper top-nav-inner">
        <a class="brand" href="<?= site_url('client/dashboard') ?>">
            <div>
                <strong> Mobile Money</strong>
                <span>Transferts rapides et sécurisés</span>
            </div>
        </a>

        <nav class="top-nav-links" aria-label="Navigation principale">
            <a class="nav-link <?= url_is('client/dashboard') ? 'active' : '' ?>" href="<?= site_url('client/dashboard') ?>">
                <i class="bi bi-grid me-1"></i>Tableau de bord
            </a>
            <a class="nav-link <?= url_is('client/deposit') ? 'active' : '' ?>" href="<?= site_url('client/deposit') ?>">
                <i class="bi bi-wallet2 me-1"></i>Dépôt
            </a>
            <a class="nav-link <?= url_is('client/withdraw') ? 'active' : '' ?>" href="<?= site_url('client/withdraw') ?>">
                <i class="bi bi-cash-stack me-1"></i>Retrait
            </a>
            <a class="nav-link <?= url_is('client/transfer') ? 'active' : '' ?>" href="<?= site_url('client/transfer') ?>">
                <i class="bi bi-send me-1"></i>Transfert
            </a>
            <a class="nav-link <?= url_is('client/history') ? 'active' : '' ?>" href="<?= site_url('client/history') ?>">
                <i class="bi bi-clock-history me-1"></i>Historique
            </a>
            <a class="nav-link danger" href="<?= site_url('logout') ?>">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </a>
        </nav>
    </div>
</header>
<?php endif; ?>

<div class="app-wrapper">
    <div class="page-shell">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-custom alert-success-custom d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span><?= session()->getFlashdata('success') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-custom alert-danger-custom d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <span><?= session()->getFlashdata('error') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>