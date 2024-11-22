<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;
    protected $fillable = [
        'pay_method_id',  //id de metodos de pago
        'company_id',//id de la compania
        'is_depretation_a',//si es depreciacion acelerada
        'is_legal',//si tiene sustento legal
        'vaucher', //numero de comprobante
        'date_acquisition',//fecha de adquisicion
        'detail',//detalle de activo fijo
        'code',//codigo de activo fijo
        'type_id',//tipo de activo fijo
        'address', // direccion o localizacion del activo fijo
        'period',//tiempo de depreciacion en anios
        'value', //valor  del af
        'residual_value', //valor residual del af
        'date_end',//fecha de finalizacion de la depreciacion
    ];
    protected $casts = [
        'is_depretation_a' => 'boolean',
        'is_legal' => 'boolean',
        'date_acquisition' => 'date',
        'period' => 'integer',
      //  'value' => 'decimal',
        //'residual_value' => 'decimal',
        'date_end' => 'date',
    ];
    public function paymethod()
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function activetype()
    {
        return $this->belongsTo(ActiveTypes::class);
    }
}

