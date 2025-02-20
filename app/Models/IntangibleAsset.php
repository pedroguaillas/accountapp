<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntangibleAsset extends Model
{
    use HasFactory,  SoftDeletes;
    protected $fillable = [
        'pay_method_id',  //id de metodos de pago
        'company_id',//id de la compania
        'is_legal',//si tiene sustento legal
        'vaucher', //numero de comprobante
        'date_acquisition',//fecha de adquisicion
        'detail',//detalle de activo fijo
        'code',//codigo de activo fijo
        'type',//tipo de activo fijo
        'period',//tiempo de depreciacion en anios
        'value', //valor  del af
        'date_end',//fecha de finalizacion de la depreciacion
    ];
    
    protected $casts = [
        'is_legal' => 'boolean',
        'date_acquisition' => 'date',
        'period' => 'integer',
        'value' => 'float',
        'date_end' => 'date',
    ];

    public function paymethod()
    {
        return $this->belongsTo(PayMethod::class);
    }

}

