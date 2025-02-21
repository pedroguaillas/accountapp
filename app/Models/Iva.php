<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        'percentage',
    ];

    protected $casts = [
        'code' => 'integer',
        'percentage' => 'float',
    ];
}