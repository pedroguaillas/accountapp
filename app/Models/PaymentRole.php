<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', // ID de la compañía
        'employee_id', // ID del empleado
        'position', // Cargo
        'sector_code', // Código sectorial
        'days', // Días
        'salary', // Salario
        'salary_receive', // Salario
        'date',//fecha de generacion del rol
        'state',//estado
    ];

    protected $casts = [
        'days' => 'integer',
        'salary' => 'float',
        'salary_receive' => 'float',//sala
    ];

    // Relación con el modelo Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relación con el modelo Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function paymentroleingresses()
    {
        return $this->hasMany(PaymentRoleIngress::class);
    }

    public function paymentroleegresses()
    {
        return $this->hasMany(PaymentRoleEgress::class);
    }
}
