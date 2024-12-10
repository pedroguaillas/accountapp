<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        'account_id',   // Vinculo con la cuenta
    ];

    protected $casts = [
        'code' => 'integer',
        'account_id' => 'integer',
    ];
}
