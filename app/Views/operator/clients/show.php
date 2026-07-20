<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <a href="<?= site_url('operator/clients') ?>" class="text-dark text-decoration-none me-2"><i class="bi bi-arrow-left"></i></a>
        <i class="bi bi-person-circle me-2" style="color: #0C4650;"></i>
        Détails du Client: <?= htmlspecialchars($client['phone_number']) ?>
    </h1>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card balance-card">
            <div class="card-body text-center">
                <h6>Solde Actuel</h6>
                <h2 class="fw-bold"><?= number_format($client['balance'], 2, ',', ' ') ?> Ar</h2>
                <small>Créé le <?= date('d/m/Y H:i', strtotime($client['created_at'])) ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Informations</h5>
                <p class="mb-1"><strong>ID :</strong> <?= $client['id'] ?></p>
                <p class="mb-1"><strong>Statut :</strong> 
                    <?php if($client['status'] == 'active'): ?>
                        <span class="badge bg-success">Actif</span>
                    <?php else: ?>
                        <span class="badge bg-danger"><?= htmlspecialchars($client['status']) ?></span>
                    <?php endif; ?>
                </p>
                <p class="mb-0"><strong>Dernière mise à jour :</strong> <?= date('d/m/Y H:i', strtotime($client['updated_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Historique des Transactions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Réf</th>
                        <th>Opération</th>
                        <th>Montant (Ar)</th>
                        <th>Frais (Ar)</th>
                        <th>Détails</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $t): ?>
                        <?php 
                            $isSender = ($t['sender_client_id'] == $client['id']);
                            $sign = $isSender ? '-' : '+';
                            $color = $isSender ? 'text-danger' : 'text-success';
                            if ($t['operation_code'] == 'DEPOSIT') {
                                $sign = '+';
                                $color = 'text-success';
                            } elseif ($t['operation_code'] == 'WITHDRAWAL') {
                                $sign = '-';
                                $color = 'text-danger';
                            }
                        ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                        <td><small><?= $t['transaction_reference'] ?></small></td>
                        <td>
                            <span class="badge" style="background-color: #898B8F; color: white; border: 2px solid black;"><?= $t['operation_code'] ?></span>
                        </td>
                        <td class="fw-bold <?= $color ?>">
                            <?= $sign ?> <?= number_format($t['amount'], 2, ',', ' ') ?>
                        </td>
                        <td><?= number_format($t['fee_amount'], 2, ',', ' ') ?></td>
                        <td>
                            <small>
                            <?php if($t['operation_code'] == 'TRANSFER'): ?>
                                <?php if($isSender): ?>
                                    Vers: <?= $t['receiver_phone'] ?>
                                <?php else: ?>
                                    De: <?= $t['sender_phone'] ?>
                                <?php endif; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge" style="background-color: #1fa25c; color: white; border: 2px solid black;"><?= $t['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="text-center p-3">Aucune transaction trouvée.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager->getPageCount() > 1): ?>
    <div class="card-footer">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
