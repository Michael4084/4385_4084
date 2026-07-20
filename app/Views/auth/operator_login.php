<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    
</head>
<body>

<button class="theme-toggle" onclick="toggleTheme()">
    <i class="bi bi-moon" id="theme-icon"></i>
    <span id="theme-text">Sombre</span>
</button>

<div class="login-wrapper">
    <div class="login-card">
        <!-- En-tête avec icône -->
        <div class="text-center mb-4">
            <div class="icon-wrapper">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h4 class="fw-bold mb-1">Administration</h4>
            <p class="text-muted small mb-0">Espace réservé aux opérateurs</p>
        </div>
        
        <!-- Messages d'erreur -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form action="<?= site_url('operator/login') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Nom d'utilisateur</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" required placeholder="Entrez votre nom d'utilisateur">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="Entrez votre mot de passe">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>
        </form>
        
        <!-- Lien retour -->
        <div class="text-center mt-4">
            <a href="<?= site_url('login') ?>" class="text-secondary text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Retour au site client
            </a>
        </div>
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