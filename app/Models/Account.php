<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', // ID de la compania
        'code', // Codigo de la cuenta
        'name', // Nombre de la cuenta
        'parent_id', // Si tiene una cuenta padre
        'type', // activo, pasivo, patrimonio, ingreso, gasto
        'is_active',
        'is_detail',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_detail' => 'boolean',
        ];
    }
}
