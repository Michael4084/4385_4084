<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionMdel extends Model
{
    protected $table            = 'promotion';
    protected $primaryKey       = 'id_promotion';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['operator_id','amount'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';
}

