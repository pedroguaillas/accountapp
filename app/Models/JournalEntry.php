<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id', // ID del asiento
        'account_id', // ID de la cuenta
        'cost_center_id', // ID centro de costos
        'debit', // Monto del debe
        'have', // Monto del haber
    ];

    protected function casts(): array
    {
        return [
            'debit' => 'decimal',
            'have' => 'decimal',
        ];
    }
}
