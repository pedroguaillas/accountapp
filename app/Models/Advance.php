<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advance extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'detail',                 // Categoria del activo fijo
        'amount',    // Tiempo de depreciacion
        'employee_id',           // Vinculo con el empleado
        'company_id',//cinculo con la compania

    ];

    protected $casts = [
        'amount' => 'float',
        'employee_id' => 'integer',
        'company_id' => 'integer',
    ];
}
