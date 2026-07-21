<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MobileMoney_v2_Enhancements extends Migration
{
    public function up()
    {
        // Add operator_code column to operators table
        $this->forge->addColumn('operators', [
            'operator_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'after'      => 'password_hash'
            ],
        ]);

        // 1. Create operator_prefixes table (v2)
        // Each operator can manage multiple phone prefixes
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'operator_id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'prefix'      => ['type' => 'VARCHAR', 'constraint' => '10'],
            'is_active'   => ['type' => 'BOOLEAN', 'default' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operator_id', 'operators', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['operator_id', 'prefix']);
        $this->forge->createTable('operator_prefixes');

        // 2. Create operator_commissions table (v2)
        // Store % commission for inter-operator transfers
        $this->forge->addField([
            'id'                      => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'operator_id'             => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'operation_type_id'       => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'commission_percentage'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
            'min_amount'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'max_amount'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'created_at'              => ['type' => 'DATETIME', 'null' => true],
            'updated_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operator_id', 'operators', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operation_type_id', 'operation_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('operator_commissions');

        // 3. Add columns to transactions table for v2 features
        $this->forge->addColumn('transactions', [
            'sender_operator_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'receiver_client_id'
            ],
            'receiver_operator_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'sender_operator_id'
            ],
            'inter_operator_commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'after'      => 'fee_amount'
            ],
            'include_withdrawal_fee' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'after'      => 'inter_operator_commission'
            ],
        ]);

        // Add foreign keys for new operator columns
        $this->forge->addForeignKey('sender_operator_id', 'operators', 'id', 'SET NULL', 'SET NULL', 'fk_trans_sender_op');
        $this->forge->addForeignKey('receiver_operator_id', 'operators', 'id', 'SET NULL', 'SET NULL', 'fk_trans_receiver_op');

        // 4. Create transaction_recipients table (v2)
        // Support for multiple recipients in a single transfer
        $this->forge->addField([
            'id'                   => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'transaction_id'       => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'receiver_phone_number' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'amount'               => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addIndex('transaction_id');
        $this->forge->createTable('transaction_recipients');
    }

    public function down()
    {
        // Drop new tables in reverse order
        $this->forge->dropTable('transaction_recipients');

        // Remove columns from transactions
        $this->forge->dropColumn('transactions', 'include_withdrawal_fee');
        $this->forge->dropColumn('transactions', 'inter_operator_commission');
        $this->forge->dropColumn('transactions', 'receiver_operator_id');
        $this->forge->dropColumn('transactions', 'sender_operator_id');

        // Drop new tables
        $this->forge->dropTable('operator_commissions');
        $this->forge->dropTable('operator_prefixes');

        // Remove operator_code from operators
        $this->forge->dropColumn('operators', 'operator_code');
    }
}
