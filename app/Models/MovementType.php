<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovementType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'account_id', 
        'name', // Nombre comercial si tiene
        'code',//codigo
        'type', // tipo Ingreso oEgreso
        'venue',//caja,banco o ambos
    ];
}