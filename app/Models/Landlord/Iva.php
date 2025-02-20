<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
        'fee',
    ];

    protected $casts = [
        'code' => 'integer',
        'fee' => 'float',
    ];
}
