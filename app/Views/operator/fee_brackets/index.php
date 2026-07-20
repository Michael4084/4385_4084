<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <i class="bi bi-currency-exchange fs-2 me-3" style="color: #0C4650;"></i>
        <h1 class="h2 mb-0">Barèmes de frais</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ajouter une tranche</h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url('operator/fees') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Type d'opération</label>
                        <select name="operation_type_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <?php foreach($operationTypes as $op): ?>
                                <option value="<?= $op['id'] ?>"><?= $op['name'] ?> (<?= $op['code'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant Min (Ar)</label>
                        <input type="number" name="min_amount" class="form-control" value="<?= old('min_amount', '0') ?>" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant Max (Ar)</label>
                        <input type="number" name="max_amount" class="form-control" value="<?= old('max_amount') ?>" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frais (Ar)</label>
                        <input type="number" name="fee_amount" class="form-control" value="<?= old('fee_amount', '0') ?>" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Opération</th>
                                <th>Min (Ar)</th>
                                <th>Max (Ar)</th>
                                <th>Frais (Ar)</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($brackets as $b): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($b['operation_name']) ?></strong></td>
                                <td><?= number_format($b['min_amount'], 2, ',', ' ') ?></td>
                                <td><?= number_format($b['max_amount'], 2, ',', ' ') ?></td>
                                <td><span class="text-danger fw-bold"><?= number_format($b['fee_amount'], 2, ',', ' ') ?></span></td>
                                <td class="text-end">
                                    <form action="<?= site_url('operator/fees/delete/'.$b['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Supprimer ce barème ?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm" style="background-color: #e8453c; color: white; border: 2px solid black;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($brackets)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun barème trouvé.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
