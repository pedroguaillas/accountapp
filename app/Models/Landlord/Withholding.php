<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withholding extends Model
{
    use HasFactory;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'code',
        'name',
        'fee',
        'type',
    ];

    protected $casts = [
        'code' => 'integer',
        'fee' => 'float',
    ];
}
