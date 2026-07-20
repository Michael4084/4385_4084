<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container px-0">
    <!-- En-tête avec bienvenue -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h5 class="fw-bold mb-0">Bonjour 👋</h5>
            <small class="text-muted">Bienvenue sur votre espace Mobile Money</small>
        </div>
        <div class="bg-light rounded-circle p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-person fs-5 text-primary"></i>
        </div>
    </div>

    <!-- Carte de solde -->
    <div class="card border-0 rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #0C4650, #1a7a8a);">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <small class="opacity-75">Solde disponible</small>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($client['balance'], 2, ',', ' ') ?> <small class="fs-6">Ar</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <small class="opacity-75"><i class="bi bi-phone me-1"></i> <?= htmlspecialchars($client['phone_number']) ?></small>
                <span class="badge bg-light text-dark opacity-75">Compte actif</span>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row g-3">
        <div class="col-6">
            <a href="<?= site_url('client/deposit') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-4 text-center p-3 action-card h-100 shadow-sm" style="background: #e8f5e9; transition: transform 0.2s;">
                    <div class="bg-success bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="bi bi-box-arrow-in-down fs-3 text-success"></i>
                    </div>
                    <h6 class="mt-2 fw-bold mb-0">Dépôt</h6>
                    <small class="text-muted">Ajouter des fonds</small>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="<?= site_url('client/withdraw') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-4 text-center p-3 action-card h-100 shadow-sm" style="background: #fce4ec; transition: transform 0.2s;">
                    <div class="bg-danger bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="bi bi-box-arrow-up fs-3 text-danger"></i>
                    </div>
                    <h6 class="mt-2 fw-bold mb-0">Retrait</h6>
                    <small class="text-muted">Retirer des fonds</small>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="<?= site_url('client/transfer') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-4 text-center p-3 action-card h-100 shadow-sm" style="background: #e3f2fd; transition: transform 0.2s;">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="bi bi-send fs-3 text-primary"></i>
                    </div>
                    <h6 class="mt-2 fw-bold mb-0">Transfert</h6>
                    <small class="text-muted">Envoyer de l'argent</small>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="<?= site_url('client/history') ?>" class="text-decoration-none">
                <div class="card border-0 rounded-4 text-center p-3 action-card h-100 shadow-sm" style="background: #fff3e0; transition: transform 0.2s;">
                    <div class="bg-warning bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="bi bi-clock-history fs-3 text-warning"></i>
                    </div>
                    <h6 class="mt-2 fw-bold mb-0">Historique</h6>
                    <small class="text-muted">Voir les transactions</small>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
</style>
<?= $this->endSection() ?>