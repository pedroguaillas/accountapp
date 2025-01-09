<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',   // Compania extends
        'account_id',   // Vinculo con la cuenta contable
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        
    ];

    protected $casts = [
        'account_id' => 'integer',
        'code' => 'integer',
    ];
}
