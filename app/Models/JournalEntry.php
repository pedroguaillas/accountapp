<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_id', // ID del asiento
        'account_id', // ID de la cuenta
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
