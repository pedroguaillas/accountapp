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
        
        'overtime', // Horas extras
        'ordinary_hours', // Horas ordinarias
        'advance_utility', // Adelanto de utilidades
        'remuneration', // Remuneración
        'food_expenses', // Gastos de alimentación
        'iess_personal', // Aporte personal al IESS
        'xiii', // Décimo tercero
        'xiv', // Décimo cuarto
        'reserve_funds', // Fondos de reserva

        'advance_salary', // Adelanto de salario
        'fines', // Multas
        'company_loan', // Préstamos de la compañía
        'iess_patronal', // Aporte patronal al IESS
        'judicial_retention', // Retención judicial
        'sri_tax_withholding', // Retención de impuestos SRI
        'salary_receive', // Salario a recibir
        'date',//fecha de generacion del rol
    ];

    protected $casts = [
        'company_id' => 'integer',
        'employee_id' => 'integer',
        'days' => 'integer',
        'salary' => 'float',
        'overtime' => 'integer',
        'ordinary_hours' => 'integer',
        'advance_utility' => 'float',
        'remuneration' => 'float',
        'food_expenses' => 'float',
        'advance_salary' => 'float',
        'iess_personal' => 'float',
        'xiii' => 'float',
        'xiv' => 'float',
        'reserve_funds' => 'float',
        'fines' => 'float',
        'company_loan' => 'float',
        'iess_patronal' => 'float',
        'judicial_retention' => 'float',
        'sri_tax_withholding' => 'float',
        'salary_receive' => 'float',
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

    public function paymentroleingress()
    {
        return $this->hasMany(PaymentRoleIngress::class);
    }

    public function paymentroleegress()
    {
        return $this->hasMany(PaymentRoleEgress::class);
    }
}
