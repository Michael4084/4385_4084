<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Section image en haut sur mobile, à gauche sur desktop -->
                    <div class="col-12 col-md-5 order-md-1">
                        <div class="bg-primary h-100 d-flex align-items-center justify-content-center p-4" 
                             style="background: linear-gradient(135deg, #0C4650, #1a7a8a); min-height: 200px;">
                            <div class="text-center text-white">
                                <i class="bi bi-phone fs-1 mb-3 d-block"></i>
                                <h4 class="fw-bold">Mobile Money</h4>
                                <p class="mb-0 small opacity-75">Transferts d'argent simplifiés</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section formulaire -->
                    <div class="col-12 col-md-7 order-md-2 p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Bienvenue</h3>
                            <p class="text-muted small">Connectez-vous avec votre numéro de téléphone</p>
                        </div>
                        
                        <form action="<?= site_url('login') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-telephone text-primary"></i>
                                    </span>
                                    <input type="text" name="phone_number" 
                                           class="form-control border-start-0 ps-0" 
                                           placeholder="0340000001" 
                                           required pattern="^[0-9]{10}$" 
                                           title="Veuillez saisir un numéro à 10 chiffres."
                                           style="font-size: 0.95rem;">
                                </div>
                                <div class="form-text small">Format : 10 chiffres (ex: 0340000001)</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Accéder à mon compte
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="<?= site_url('operator/login') ?>" class="text-secondary text-decoration-none small">
                                <i class="bi bi-shield-lock me-1"></i>Espace Opérateur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>