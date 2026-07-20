<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['phone_number', 'balance', 'status'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'phone_number' => 'required|is_unique[clients.phone_number,id,{id}]|regex_match[/^[0-9]{10}$/]',
        'balance'      => 'numeric|greater_than_equal_to[0]'
    ];
    protected $validationMessages   = [
        'phone_number' => [
            'is_unique'   => 'Ce numéro de téléphone est déjà utilisé.',
            'regex_match' => 'Le format du numéro de téléphone est invalide.'
        ]
    ];
    protected $skipValidation       = false;
}
