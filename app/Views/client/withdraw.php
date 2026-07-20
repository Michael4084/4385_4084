<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container px-0">
    <div class="card border-0 rounded-4 shadow-sm mt-4 overflow-hidden">
        <!-- En-tête avec fond dégradé -->
        <div class="p-4" style="background: linear-gradient(135deg, #b71c1c, #d32f2f);">
            <div class="d-flex align-items-center">
                <a href="<?= site_url('client/dashboard') ?>" class="text-white me-3 opacity-75">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                    <i class="bi bi-box-arrow-up fs-4 text-white"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white fw-bold">Effectuer un Retrait</h5>
                    <small class="text-white-50">Retirez des fonds de votre compte</small>
                </div>
            </div>
        </div>

        <!-- Corps du formulaire -->
        <div class="p-4">
            <!-- Info solde -->
            <div class="alert alert-light border-0 bg-light rounded-3 d-flex justify-content-between align-items-center">
                <span class="text-muted"><i class="bi bi-wallet2 me-2"></i>Solde disponible</span>
                <strong class="fs-5"><?= number_format($client['balance'], 2, ',', ' ') ?> Ar</strong>
            </div>

            <form action="<?= site_url('client/withdraw') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Montant à retirer</label>
                    <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                        <input type="number" name="amount" class="form-control border-0 fw-bold" placeholder="Saisir le montant" min="1" required>
                        <span class="input-group-text border-0 bg-light fw-bold">Ar</span>
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i> Les frais de retrait seront déduits de votre solde</div>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold rounded-3">
                    <i class="bi bi-check-circle me-2"></i>Valider le retrait
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>