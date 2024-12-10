<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,  SoftDeletes;
    protected $fillable = [
        'company_id',//id de la compania
        'cuit',//cedula
        'name', //nombre
        'sector_code',//codigo sectorial
        'post',//cargo
        'days',//dias
        'salary',//salario
        'porcent_aportation',//porcentaje de aportacion 
        'is_a_parner', //es socio o duenio
        'is_a_qualified_craftsman',//es artesano calificado
        'affiliated_with_spouse',//tiene afiliacion a su esposa
        'date_start',// fecha de inicio
    ];
    protected $casts = [
        'is_a_parner' => 'boolean',
        'is_a_qualified_craftsman' => 'boolean',
        'affiliated_with_spouse' => 'boolean',
        'date_start' => 'date',
        'days' => 'integer',
        'salary' => 'float',
        'porcent_aportation' => 'float',
    ];

}

