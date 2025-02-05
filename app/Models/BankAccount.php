<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id',
        'company_id',
        'account_number',
        'account_type',
        'current_balance',
        'state',
    ];
    protected $casts = [
        'current_balance' => 'float',
        'state' => 'boolean',
    ];

    // Relación con el banco
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    // Relación con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getStatusAttribute()
    {
        return $this->state ? 'Activo' : 'Inactivo';
    }
}
