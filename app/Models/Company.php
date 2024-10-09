<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'company',
        'economic_activity',
        'type',
        'date',
        'special',
        'accounting',
        'phone',
        'cert_dir',
        'pass_cert',
        'sign_valid_from',
        'sign_valid_to',
        'enviroment_type'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'special' => 'integer',
            'accounting' => 'boolean',
            'enviroment_type' => 'integer'
        ];
    }

}
