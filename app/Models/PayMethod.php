<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        'max',
    ];

    protected $casts = [
        'code' => 'integer',
        'max' => 'float',
    ];
}
