<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,  SoftDeletes;

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

    public function paymethods()
    {
        return $this->hasMany(PayMethod::class);//relacion muchos a muchos 
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);//relacion muchos a muchos 
    }
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function fixedassets()
    {
        return $this->hasMany(FixedAsset::class);
    }

    public function intangibleassets()
    {
        return $this->hasMany(IntangibleAsset::class);
    }

    public function employee()
    {
        return $this->hasMany(Employee::class);
    }
}
