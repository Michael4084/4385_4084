<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMobileMoneyTables extends Migration
{
    public function up()
    {
        // 1. operators
        $this->forge->addField([
            'id'            => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operators');

        // 2. phone_prefixes
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prefix'     => ['type' => 'VARCHAR', 'constraint' => '10', 'unique' => true],
            'is_active'  => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('phone_prefixes');

        // 3. operation_types
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'is_active'  => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operation_types');

        // 4. fee_brackets
        $this->forge->addField([
            'id'                => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'operation_type_id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'min_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'max_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'fee_amount'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operation_type_id', 'operation_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fee_brackets');

        // 5. clients
        $this->forge->addField([
            'id'           => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'phone_number' => ['type' => 'VARCHAR', 'constraint' => '20', 'unique' => true],
            'balance'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'status'       => ['type' => 'VARCHAR', 'constraint' => '20', 'default' => 'active'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('clients');

        // 6. transactions
        $this->forge->addField([
            'id'                    => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'transaction_reference' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'operation_type_id'     => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true],
            'sender_client_id'      => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'receiver_client_id'    => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'amount'                => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'fee_amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'total_amount'          => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'balance_before'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'balance_after'         => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'status'                => ['type' => 'VARCHAR', 'constraint' => '20', 'default' => 'completed'],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operation_type_id', 'operation_types', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('sender_client_id', 'clients', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('receiver_client_id', 'clients', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
        $this->forge->dropTable('clients');
        $this->forge->dropTable('fee_brackets');
        $this->forge->dropTable('operation_types');
        $this->forge->dropTable('phone_prefixes');
        $this->forge->dropTable('operators');
    }
}
