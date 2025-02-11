<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmisionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', // ID Establecimiento
        'name', // Para reconocer el Punto de Emision (ej. Junanito)
        'number', // Numero del punto de emision
        // COLUMNAS PARA ACTUALIZAR LA SECUENCIA DE LOS COMPROBANTES
        'invoice',
        'credit_note',
        'debit_note',
        'retention',
        'referral_guide',
        'purchase_settlement',
        'lot',
        'order_note',
        'proforma',
        'data_additional',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'invoice' => 'integer',
            'credit_note' => 'integer',
            'debit_note' => 'integer',
            'retention' => 'integer',
            'referral_guide' => 'integer',
            'purchase_settlement' => 'integer',
            'lot' => 'integer',
            'order_note' => 'integer',
            'proforma' => 'integer',
            'data_additional' => 'array',//devolver un array asociativo
        ];
    }
}
