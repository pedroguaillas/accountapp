<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        "company_id", // ID Compania
        "code", // Codigo de centro de costos
        "name", // Nombre de centro de costos
        "parent_id", // Si tiene un ID padre de Centro de Costos
        "is_active",
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
