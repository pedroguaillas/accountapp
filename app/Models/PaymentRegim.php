<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRegim extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'region', 
        'months_xiii', // Meses para el décimo tercero
        'months_xiv', // Meses para el décimo cuarto
        'months_reserve_funds', // Meses para fondos de reserva
    ];
}