<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRoleEgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_role_id', // ID del empleado
        'role_egress_id', // ID del empleado
        'value', // Salario
        'data_additional',
    ];

    protected $casts = [
        'value' => 'float',
        'data_additional' => 'array',//devolver un array asociativo
    ];

    // Relación con el modelo Employee
    public function paymentrole()
    {
        return $this->belongsTo(PaymentRole::class);
    }

    public function roleEgress()
    {
        return $this->belongsTo(RoleEgress::class); // Relación con RoleEgress
    }
}