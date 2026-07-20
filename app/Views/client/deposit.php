<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container px-0">
    <div class="card border-0 rounded-4 shadow-sm mt-4 overflow-hidden">
        <!-- En-tête avec fond dégradé -->
        <div class="p-4" style="background: linear-gradient(135deg, #0C4650, #1a7a8a);">
            <div class="d-flex align-items-center">
                <a href="<?= site_url('client/dashboard') ?>" class="text-white me-3 opacity-75">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                    <i class="bi bi-box-arrow-in-down fs-4 text-white"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white fw-bold">Effectuer un Dépôt</h5>
                    <small class="text-white-50">Ajoutez des fonds à votre compte</small>
                </div>
            </div>
        </div>

        <!-- Corps du formulaire -->
        <div class="p-4">
            <form action="<?= site_url('client/deposit') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Montant à déposer</label>
                    <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                        <input type="number" name="amount" class="form-control border-0 fw-bold" placeholder="Saisir le montant" min="1" required>
                        <span class="input-group-text border-0 bg-light fw-bold">Ar</span>
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i> Montant minimum : 1 Ar</div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3">
                    <i class="bi bi-check-circle me-2"></i>Valider le dépôt
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>