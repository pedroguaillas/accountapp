<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRegim extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'region', 
        'months_xiii', // Meses para el décimo tercero
        'months_xiv', // Meses para el décimo cuarto
        'months_reserve_funds', // Meses para fondos de reserva
    ];
}