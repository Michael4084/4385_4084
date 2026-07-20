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
<body>

<!-- Bouton thème -->
<button type="button" class="theme-toggle" onclick="toggleTheme()">
    <i class="bi bi-moon" id="theme-icon"></i>
    <span id="theme-text" class="ms-1">Sombre</span>
</button>

<?php if(session()->get('client_id')): ?>
<!-- Navbar personnalisée -->
<nav class="navbar-custom">
    <div class="app-wrapper">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand" href="<?= site_url('client/dashboard') ?>">
                <img src="/assets/utils/Gemini_Generated_Image_om0k4som0k4som0k.png" alt="Mobile Money Logo">
            </a>
            
            <!-- Menu mobile toggle -->
            <button class="btn btn-link text-white d-lg-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <!-- Menu desktop -->
            <div class="d-none d-lg-flex align-items-center gap-2">
                <a class="nav-link" href="<?= site_url('client/dashboard') ?>">
                    <i class="bi bi-grid me-1"></i>Tableau de bord
                </a>
                <a class="nav-link" href="<?= site_url('client/history') ?>">
                    <i class="bi bi-clock-history me-1"></i>Historique
                </a>
                <a class="nav-link text-danger" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Menu mobile -->
        <div class="collapse" id="mobileMenu">
            <div class="pt-3 pb-2 d-flex flex-column gap-1">
                <a class="nav-link" href="<?= site_url('client/dashboard') ?>">
                    <i class="bi bi-grid me-2"></i>Tableau de bord
                </a>
                <a class="nav-link" href="<?= site_url('client/history') ?>">
                    <i class="bi bi-clock-history me-2"></i>Historique
                </a>
                <a class="nav-link text-danger" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Contenu principal -->
<div class="app-wrapper">
    <!-- Messages flash -->
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

    <!-- Contenu de la page -->
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        const icon = document.getElementById('theme-icon');
        const text = document.getElementById('theme-text');
        
        if (document.body.classList.contains('dark-mode')) {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
            text.textContent = 'Clair';
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
            text.textContent = 'Sombre';
            localStorage.setItem('theme', 'light');
        }
    }

    // Charger le thème sauvegardé
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-icon').classList.remove('bi-moon');
        document.getElementById('theme-icon').classList.add('bi-sun');
        document.getElementById('theme-text').textContent = 'Clair';
    }
</script>
</body>
</html>