<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleEgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', // ID de la compañí
        'name', // nombre del egreso
        'code', // codigo del egreso
        'type', // typo (fijo y otro)
        'account_departure_id',//cuenta activo
        'account_counterpart_id',//cuenta 
    ];


    // Relación con el modelo que alimenta 
    public function paymentroleegress()
{
    return $this->hasMany(PaymentRoleEgress::class); // Relación con PaymentRoleEgress
}

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
