<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovementType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // Nombre comercial si tiene
        'type', // tipo Ingreso oEgreso
    ];
}