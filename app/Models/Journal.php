<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', // fecha del asiento
        'reference', // Referencia o número del asiento
        'description', // Descripción del asiento contable
        'is_deductible', // es deducible
        'prefix', // algun prefijo
        'document_id', // ID Referencia externa (ej. ID factura)
        'table', // tabla (ej. invoices)
        'user_id', // usuario que creo el asiento
    ];

    protected function casts(): array
    {
        return [
            'date' => 'timestamp',
            'is_deductible' => 'boolean',
        ];
    }
    public function journalentries(){
        return $this->hasMany(JournalEntry::class);
    }
}
