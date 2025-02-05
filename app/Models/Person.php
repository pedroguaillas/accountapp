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
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
    ];

    // Relación con la tabla 'companies'
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
