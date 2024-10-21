<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'company',
        'economic_activity_id',
        'contributor_type_id',
        'date', // Inicio de activiades
        'restart_activities', // Reinicio de activiades (importante a la hora de facturar)
        'special', // Contribuyente especial
        'accounting',
        'retention_agent',
        'declaration_type', // mensual o semestral
        'certificate_path',
        'certificate_pass',
        'sign_valid_from',
        'sign_valid_to',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'restart_activities' => 'date',
            'special' => 'integer',
            'accounting' => 'boolean',
            'retention_agent' => 'integer',
        ];
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);//relacion muchos a muchos 
    }
}
