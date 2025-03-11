<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cash_session_id',
        'type',//ingresos y egresos
        'amount', 
        'movement_type_id',
        'beneficiary_id',
        'description',
        'data_additional',
    ];
    protected $casts = [
        'cash_session_id' => 'integer',
        'amount'=>'float',
        'data_additional' => 'array',//devolver un array asociativo
    ];

}