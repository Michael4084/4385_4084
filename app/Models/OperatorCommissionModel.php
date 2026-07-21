<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorCommissionModel extends Model
{
    protected $table            = 'operator_commissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['operator_id', 'operation_type_id', 'commission_percentage', 'min_amount', 'max_amount'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';
}
