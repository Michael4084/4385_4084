<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container px-0">
    <div class="card border-0 rounded-4 shadow-sm mt-4 overflow-hidden">
        <!-- En-tête -->
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="<?= site_url('client/dashboard') ?>" class="text-dark me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                    <i class="bi bi-clock-history fs-4 text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Historique</h5>
                    <small class="text-muted">Toutes vos transactions</small>
                </div>
            </div>
            <div>
                <form action="<?= site_url('client/history') ?>" method="get" class="d-flex">
                    <select name="type" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                        <option value="">Tous les types</option>
                        <option value="DEPOSIT" <?= isset($_GET['type']) && $_GET['type'] == 'DEPOSIT' ? 'selected' : '' ?>>Dépôts</option>
                        <option value="WITHDRAWAL" <?= isset($_GET['type']) && $_GET['type'] == 'WITHDRAWAL' ? 'selected' : '' ?>>Retraits</option>
                        <option value="TRANSFER" <?= isset($_GET['type']) && $_GET['type'] == 'TRANSFER' ? 'selected' : '' ?>>Transferts</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Liste des transactions -->
        <ul class="list-group list-group-flush">
            <?php if(empty($transactions)): ?>
                <li class="list-group-item p-5 text-center">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <h6 class="text-muted">Aucune transaction trouvée</h6>
                    <small class="text-muted">Commencez à effectuer des opérations</small>
                </li>
            <?php else: ?>
                <?php foreach($transactions as $t): ?>
                    <?php 
                        $isSender = ($t['sender_client_id'] == $clientId);
                        $isReceiver = ($t['receiver_client_id'] == $clientId);
                        
                        $sign = '+';
                        $color = 'success';
                        $bgColor = 'success';
                        $icon = 'bi-arrow-down-circle-fill text-success';
                        $desc = $t['operation_name'];

                        if ($t['operation_code'] == 'DEPOSIT') {
                            $desc = 'Dépôt';
                            $bgColor = 'success';
                            $icon = 'bi-arrow-down-circle-fill text-success';
                        } elseif ($t['operation_code'] == 'WITHDRAWAL') {
                            $sign = '-';
                            $color = 'danger';
                            $bgColor = 'danger';
                            $icon = 'bi-arrow-up-circle-fill text-danger';
                            $desc = 'Retrait';
                        } elseif ($t['operation_code'] == 'TRANSFER') {
                            if ($isSender) {
                                $sign = '-';
                                $color = 'danger';
                                $bgColor = 'danger';
                                $icon = 'bi-arrow-up-right-circle-fill text-danger';
                                $desc = 'Transfert vers ' . $t['receiver_phone'];
                            } else {
                                $color = 'success';
                                $bgColor = 'success';
                                $icon = 'bi-arrow-down-left-circle-fill text-success';
                                $desc = 'Transfert de ' . $t['sender_phone'];
                            }
                        }
                    ?>
                    <li class="list-group-item p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-<?= $bgColor ?> bg-opacity-10 rounded-circle p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi <?= $icon ?> fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= $desc ?></h6>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                                        <span class="mx-1">•</span>
                                        Réf: <?= $t['transaction_reference'] ?>
                                    </small>
                                    <?php if($t['fee_amount'] > 0 && $isSender): ?>
                                        <br><small class="text-muted"><i class="bi bi-receipt me-1"></i>Frais: <?= number_format($t['fee_amount'], 2, ',', ' ') ?> Ar</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold text-<?= $color ?>">
                                    <?= $sign ?> <?= number_format($t['amount'], 2, ',', ' ') ?> Ar
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-wallet2 me-1"></i><?= number_format($t['balance_after'], 2, ',', ' ') ?> Ar
                                </small>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        
        <!-- Pagination -->
        <?php if(!empty($transactions)): ?>
            <div class="p-3 bg-light border-top">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>