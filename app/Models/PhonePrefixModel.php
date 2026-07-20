<?php

namespace App\Models;

use CodeIgniter\Model;

class PhonePrefixModel extends Model
{
    protected $table            = 'phone_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['prefix', 'is_active'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'prefix'    => 'required|numeric|is_unique[phone_prefixes.prefix,id,{id}]',
        'is_active' => 'in_list[0,1]'
    ];
    protected $validationMessages   = [
        'prefix' => [
            'is_unique' => 'Ce préfixe existe déjà.',
            'numeric'   => 'Le préfixe doit être composé de chiffres uniquement.'
        ]
    ];
    protected $skipValidation       = false;
}
