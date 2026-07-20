<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_reference', 'operation_type_id', 'sender_client_id', 
        'receiver_client_id', 'amount', 'fee_amount', 'total_amount', 
        'balance_before', 'balance_after', 'status'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'transaction_reference' => 'required|is_unique[transactions.transaction_reference]',
        'operation_type_id'     => 'required|is_not_unique[operation_types.id]',
        'amount'                => 'required|numeric|greater_than[0]',
        'fee_amount'            => 'required|numeric|greater_than_equal_to[0]',
        'total_amount'          => 'required|numeric|greater_than[0]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
