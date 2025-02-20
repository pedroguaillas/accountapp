<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'identification',
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'data_additional',
    ];

    protected $casts = [
        'data_additional' => 'array',//devolver un array asociativo
    ];

    // Relación con la tabla 'companies'
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}