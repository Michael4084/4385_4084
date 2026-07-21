<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Operator — vérifier si admin existe déjà
        $existingAdmin = $this->db->table('operators')->where('username', 'admin')->get()->getRow();
        if (!$existingAdmin) {
            $this->db->table('operators')->insert([
                'username'      => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'operator_code' => 'TELMA',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->table('operators')->where('id', $existingAdmin->id)->update(['operator_code' => 'TELMA']);
        }

        $telmaOperator = $this->db->table('operators')->where('username', 'admin')->get()->getRow();

        // 2. Phone Prefixes — insérer seulement les préfixes manquants
        $prefixes = ['031', '032', '033', '034', '037', '038', '039'];
        foreach ($prefixes as $prefix) {
            $exists = $this->db->table('phone_prefixes')->where('prefix', $prefix)->get()->getRow();
            if (!$exists) {
                $this->db->table('phone_prefixes')->insert([
                    'prefix'     => $prefix,
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 3. Operator prefixes — Telma gère 034/038 par défaut
        $telmaPrefixes = ['034', '038'];
        foreach ($telmaPrefixes as $prefix) {
            $exists = $this->db->table('operator_prefixes')->where('operator_id', $telmaOperator->id)->where('prefix', $prefix)->get()->getRow();
            if (!$exists) {
                $this->db->table('operator_prefixes')->insert([
                    'operator_id' => $telmaOperator->id,
                    'prefix'      => $prefix,
                    'is_active'   => 1,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 4. Operation Types — insérer seulement les types manquants
        $operationTypes = [
            ['code' => 'DEPOSIT',    'name' => 'Dépôt'],
            ['code' => 'WITHDRAWAL', 'name' => 'Retrait'],
            ['code' => 'TRANSFER',   'name' => 'Transfert'],
        ];
        foreach ($operationTypes as $type) {
            $exists = $this->db->table('operation_types')->where('code', $type['code'])->get()->getRow();
            if (!$exists) {
                $this->db->table('operation_types')->insert([
                    'code'       => $type['code'],
                    'name'       => $type['name'],
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Récupérer les IDs des types d'opérations
        $depositId    = $this->db->table('operation_types')->where('code', 'DEPOSIT')->get()->getRow()->id;
        $withdrawalId = $this->db->table('operation_types')->where('code', 'WITHDRAWAL')->get()->getRow()->id;
        $transferId   = $this->db->table('operation_types')->where('code', 'TRANSFER')->get()->getRow()->id;

        // 5. Commission rules — Orange/Airtel commissions via Telma
        $commissionRules = [
            ['operator_id' => $telmaOperator->id, 'operation_type_id' => $transferId, 'commission_percentage' => 1.00, 'min_amount' => 1000, 'max_amount' => 999999999, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        foreach ($commissionRules as $rule) {
            $exists = $this->db->table('operator_commissions')->where('operator_id', $rule['operator_id'])->where('operation_type_id', $rule['operation_type_id'])->get()->getRow();
            if (!$exists) {
                $this->db->table('operator_commissions')->insert($rule);
            }
        }

        // 6. Fee Brackets — insérer seulement si la table est vide
        $existingFees = $this->db->table('fee_brackets')->countAllResults();
        if ($existingFees === 0) {
            $feeBrackets = [
                // DEPOSIT
                ['operation_type_id' => $depositId, 'min_amount' => 0, 'max_amount' => 999999999, 'fee_amount' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                
                // WITHDRAWAL
                ['operation_type_id' => $withdrawalId, 'min_amount' => 100, 'max_amount' => 1000, 'fee_amount' => 50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 1001, 'max_amount' => 5000, 'fee_amount' => 50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 5001, 'max_amount' => 10000, 'fee_amount' => 100, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 10001, 'max_amount' => 25000, 'fee_amount' => 200, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 25001, 'max_amount' => 50000, 'fee_amount' => 400, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 50001, 'max_amount' => 100000, 'fee_amount' => 800, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 100001, 'max_amount' => 250000, 'fee_amount' => 1500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 250001, 'max_amount' => 500000, 'fee_amount' => 1500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 500001, 'max_amount' => 1000000, 'fee_amount' => 2500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $withdrawalId, 'min_amount' => 1000001, 'max_amount' => 999999999, 'fee_amount' => 3000, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],

                // TRANSFER
                ['operation_type_id' => $transferId, 'min_amount' => 100, 'max_amount' => 1000, 'fee_amount' => 50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 1001, 'max_amount' => 5000, 'fee_amount' => 50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 5001, 'max_amount' => 10000, 'fee_amount' => 100, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 10001, 'max_amount' => 25000, 'fee_amount' => 200, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 25001, 'max_amount' => 50000, 'fee_amount' => 400, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 50001, 'max_amount' => 100000, 'fee_amount' => 800, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 100001, 'max_amount' => 250000, 'fee_amount' => 1500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 250001, 'max_amount' => 500000, 'fee_amount' => 1500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 500001, 'max_amount' => 1000000, 'fee_amount' => 2500, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 1000001, 'max_amount' => 2000000, 'fee_amount' => 3000, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['operation_type_id' => $transferId, 'min_amount' => 2000001, 'max_amount' => 999999999, 'fee_amount' => 5000, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ];
            $this->db->table('fee_brackets')->insertBatch($feeBrackets);
        }

        // 7. Clients — insérer seulement les clients manquants
        $clients = [
            ['phone_number' => '0340000001', 'balance' => 50000.00, 'status' => 'active'],
            ['phone_number' => '0320000002', 'balance' => 150000.00, 'status' => 'active'],
            ['phone_number' => '0330000003', 'balance' => 0.00, 'status' => 'active'],
        ];
        foreach ($clients as $client) {
            $exists = $this->db->table('clients')->where('phone_number', $client['phone_number'])->get()->getRow();
            if (!$exists) {
                $client['created_at'] = date('Y-m-d H:i:s');
                $client['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('clients')->insert($client);
            }
        }
    }
}

