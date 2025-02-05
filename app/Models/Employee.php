<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'company_id',//id de la compania
        'cuit',//cedula
        'name', //nombre
        'sector_code',//codigo sectorial
        'position',//cargo
        'days',//dias
        'salary',//salario
        'porcent_aportation',//porcentaje de aportacion 
        'is_a_parnert', //es socio o duenio
        'is_a_qualified_craftsman',//es artesano calificado
        'affiliated_with_spouse',//tiene afiliacion a su esposa
        'date_start',// fecha de inicio
        'xiii',//decimo tercero
        'xiv',//decimo cuarto
        'reserve_funds',//fondos de reserva
        'email',//correo
    ];
    protected $casts = [
        'is_a_parnert' => 'boolean',
        'is_a_qualified_craftsman' => 'boolean',
        'affiliated_with_spouse' => 'boolean',
        'date_start' => 'date',
        'days' => 'integer',
        'salary' => 'float',
        'porcent_aportation' => 'float',
        'xiii' => 'boolean',
        'xiv' => 'boolean',
        'reserve_funds' => 'boolean',
    ];


    public function advances(){
        return $this->hasMany(Advance::class);
    }

    public function paymentroles(){
        return $this->hasMany(PaymentRole::class);
    }

    public function getStatusAttribute()
    {
        return $this->state ? 'Activo' : 'Inactivo';
    }

}

