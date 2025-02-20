<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', // ID Compania  backend
        'name',
        'description',
        'state',
    ];
    protected $casts = [
        'state' => 'boolean',
    ];

    public function bankaccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function getStatusAttribute()
    {
        return $this->state ? 'Activo' : 'Inactivo';
    }
}