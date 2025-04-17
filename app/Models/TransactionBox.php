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
        'amount', 
        'movement_type_id',
        'beneficiary_id',
        'description',
        'data_additional',
        'box_id',
        'document',

    ];
    protected $casts = [
        'cash_session_id' => 'integer',
        'amount'=>'float',
        'data_additional' => 'array',//devolver un array asociativo
    ];

}