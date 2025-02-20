<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ice extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        'fee',
        'specific_fee',
    ];

    protected $casts = [
        'code' => 'integer',
        'fee' => 'float',
        'specific_fee' => 'float',
    ];
}
