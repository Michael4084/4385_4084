<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeBracketModel extends Model
{
    protected $table            = 'fee_brackets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['operation_type_id', 'min_amount', 'max_amount', 'fee_amount'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'operation_type_id' => 'required|is_not_unique[operation_types.id]',
        'min_amount'        => 'required|numeric|greater_than_equal_to[0]',
        'max_amount'        => 'required|numeric|greater_than_equal_to[0]',
        'fee_amount'        => 'required|numeric|greater_than_equal_to[0]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
