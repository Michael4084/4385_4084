<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <i class="bi bi-people fs-2 me-3" style="color: #0C4650;"></i>
        <h1 class="h2 mb-0">Comptes Clients</h1>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('operator/clients') ?>" class="d-flex w-50">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher un numéro de téléphone..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            <?php if($search): ?>
                <a href="<?= site_url('operator/clients') ?>" class="btn btn-secondary ms-2">Effacer</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Téléphone</th>
                        <th>Solde (Ar)</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><strong><?= htmlspecialchars($c['phone_number']) ?></strong></td>
                        <td class="fw-bold text-success"><?= number_format($c['balance'], 2, ',', ' ') ?></td>
                        <td>
                            <?php if($c['status'] == 'active'): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><?= htmlspecialchars($c['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('operator/clients/'.$c['id']) ?>" class="btn btn-sm" style="background-color: #0C4650; color: white; border: 2px solid black;">
                                <i class="bi bi-eye"></i> Historique
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($clients)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun client trouvé.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?= $pager->links() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
