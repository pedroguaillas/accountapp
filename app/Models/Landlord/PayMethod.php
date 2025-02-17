<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'code',         // Codigo del metodo de pago
        'name',         // Nombre del method de pago
    ];

    protected $casts = [
        'account_id' => 'integer',
        'code' => 'integer',
    ];
}
