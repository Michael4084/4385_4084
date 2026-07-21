<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <i class="bi bi-percent fs-2 me-3" style="color: #0C4650;"></i>
        <h1 class="h2 mb-0">Commissions inter-opérateurs</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ajouter une commission</h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url('operator/commissions') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Opérateur</label>
                        <select name="operator_id" class="form-select" required>
                            <?php foreach($operators as $operator): ?>
                                <option value="<?= $operator['id'] ?>"><?= htmlspecialchars($operator['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type d’opération</label>
                        <select name="operation_type_id" class="form-select" required>
                            <?php foreach($operationTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pourcentage (%)</label>
                        <input type="number" step="0.01" name="commission_percentage" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant min</label>
                            <input type="number" step="0.01" name="min_amount" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant max</label>
                            <input type="number" step="0.01" name="max_amount" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Opérateur</th>
                                <th>Type</th>
                                <th>%</th>
                                <th>Tranche</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($commissions as $commission): ?>
                                <tr>
                                    <td><?= htmlspecialchars($commission['operator_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($commission['operation_name'] ?? '') ?></td>
                                    <td><?= number_format($commission['commission_percentage'], 2, ',', ' ') ?> %</td>
                                    <td><?= $commission['min_amount'] !== null ? number_format($commission['min_amount'], 2, ',', ' ') . ' - ' . number_format($commission['max_amount'], 2, ',', ' ') : 'Tous' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>