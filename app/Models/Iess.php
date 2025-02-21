<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iess extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'percentage',
    ];

    protected $casts = [
        'percentage' => 'float',
    ];
}