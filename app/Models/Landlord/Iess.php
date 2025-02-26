<?php

namespace App\Models\Landlord;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iess extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global
    
    protected $fillable = [
        'code',
        'type',
        'name',
        'percentage',
    ];

    protected $casts = [
        //'percentage' => 'float',
    ];
}