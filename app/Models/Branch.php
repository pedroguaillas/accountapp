<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', // ID Compania
        'name', // Nombre comercial si tiene
        'number', // Numer de establecimiento
        'city', // Provicia
        'phone',
        'address',
        'logo_path',
        'is_matriz',
        'enviroment_type', // Ambiente 1-Pruebas, 2-Produccion
        'email', // Correo a utilizar para el envio de correos
        'pass_email', // Contraseñ a utilizar para el envio de correos
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'is_matriz' => 'boolean',
            'enviroment_type' => 'integer',
        ];
    }
}
