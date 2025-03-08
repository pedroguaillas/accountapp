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
        'transaction_date', // Transaction date and time
        'type',//ingresos y egresos
        'amount', 
        'description',
        'data_additional',
    ];
    protected $casts = [
        'cash_session_id' => 'integer',
        'transaction_date' => 'date',
        'amount'=>'float',
        'data_additional' => 'array',//devolver un array asociativo
    ];

}