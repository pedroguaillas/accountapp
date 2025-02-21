<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

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

    public function activeTypes()
    {
        return $this->hasMany(ActiveType::class);//relacion muchos a muchos 
    }

    public function paymethods()
    {
        return $this->hasMany(PayMethod::class);//relacion muchos a muchos 
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);//relacion muchos a muchos 
    }

    public function costcenters()
    {
        return $this->hasMany(CostCenter::class);//relacion muchos a muchos 
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

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function advances()
    {
        return $this->hasMany(Advance::class);
    }

    public function roleIngress()
    {
        return $this->hasMany(RoleIngress::class);
    }

    public function roleEgress()
    {
        return $this->hasMany(RoleEgress::class);
    }

    public function paymentroles()
    {
        return $this->hasMany(PaymentRole::class);
    }    

    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function movementtypes()
    {
        return $this->hasMany(MovementType::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }
}