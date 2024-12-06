<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',                 // Categoria del activo fijo
        'depreciation_time',    // Tiempo de depreciacion
        'account_id',           // Vinculo con la cuenta
    ];

    protected $casts = [
        'depreciation_time' => 'integer',
        'account_id' => 'integer',
    ];
}
