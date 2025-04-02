<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_id',      // id de gasto
        'payment_type',    // Tpo sea caja o banco
        'payment_method_id',     // id del tipo
        'amount',//monto
        'description',//Descripcion del gasto
        'document',//numero de documento o comprobante
        'date_expense',//fecha de pago del gasto
        'data_additional',//informacion adicionla
    ];

    protected $casts = [
        'amount' => 'float',
        'date_expense' =>'date',
        'payment_method_id'=>'integer',//identificador de la cuenta sea de caja o banco
        'data_additional' => 'array',//devolver un array asociativo
    ];
}