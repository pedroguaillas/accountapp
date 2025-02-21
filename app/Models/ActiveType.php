<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',                 // Categoria del activo fijo
        'depreciation_time',    // Tiempo de depreciacion
        'account_id',           // Vinculo con la cuenta
        'account_dep_id',       // Vinculo con la cuenta de Depreciación
        'account_dep_spent_id', // Vinculo con la cuenta de Gasto de Depreciación
    ];

    protected $casts = [
        'depreciation_time' => 'integer',
    ];
}