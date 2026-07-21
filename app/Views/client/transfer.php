<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container px-0">
    <div class="card border-0 rounded-4 shadow-sm mt-4 overflow-hidden">
        <!-- En-tête avec fond dégradé -->
        <div class="p-4" style="background: linear-gradient(135deg, #0d47a1, #1976d2);">
            <div class="d-flex align-items-center">
                <a href="<?= site_url('client/dashboard') ?>" class="text-white me-3 opacity-75">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                    <i class="bi bi-send fs-4 text-white"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white fw-bold">Transférer de l'argent</h5>
                    <small class="text-white-50">Envoyez des fonds à un autre utilisateur</small>
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

            <form action="<?= site_url('client/transfer') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Numéro du destinataire</label>
                    <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                        <span class="input-group-text border-0 bg-light">
                            <i class="bi bi-person text-primary"></i>
                        </span>
                        <input type="text" name="receiver_phone_number" class="form-control border-0 fw-bold" value="<?= old('receiver_phone_number') ?>" placeholder="0340000002" required pattern="^[0-9]{10}$">
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i> Format : 10 chiffres (ex: 0340000002)</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Destinataires (un par ligne, ou séparés par une virgule)</label>
                    <textarea name="receiver_phone_numbers" class="form-control" rows="4" placeholder="0340000001&#10;0320000002" required><?= old('receiver_phone_numbers') ?></textarea>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i> Vous pouvez envoyer à plusieurs numéros. Le montant sera divisé équitablement.</div>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="include_withdrawal_fee" id="include_withdrawal_fee" value="1">
                    <label class="form-check-label" for="include_withdrawal_fee">Inclure les frais de retrait</label>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Montant à transférer</label>
                    <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                        <input type="number" name="amount" class="form-control border-0 fw-bold" value="<?= old('amount') ?>" placeholder="Saisir le montant" min="1" required>
                        <span class="input-group-text border-0 bg-light fw-bold">Ar</span>
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i> Les frais de transfert seront déduits de votre solde</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3" style="background: linear-gradient(135deg, #0d47a1, #1976d2); border: none;">
                    <i class="bi bi-send me-2"></i>Valider le transfert
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>