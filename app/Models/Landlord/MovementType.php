<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovementType extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'central'; // Forzar la conexión a la base global

    protected $fillable = [
        'name', // Nombre comercial si tiene
        'code',//codigo
        'type', // tipo Ingreso oEgreso
        'venue',//caja,banco o ambos
    ];
}