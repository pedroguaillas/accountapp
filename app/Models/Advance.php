<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'detail',                 // Categoria del activo fijo
        'amount',    // Tiempo de depreciacion
        'employee_id',           // Vinculo con el empleado
        'company_id',//vinculo con la compania
        'movement_type_id',//tipo sea salario o utilidad
        'date',//fecha del anticipo
        'payment_type',//banco o caja
        'payment_method_id',//identificador de caja o banco
        'receipt_number',//numero de documento
    ];

    protected $casts = [
        'amount' => 'float',
        'date' =>'date',
        'payment_method_id'=>'integer',
    ];
}