<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'box_id',
        'open_employee_id',
        'close_employee_id',
        'initial_value',
        'ingress', 
        'egress',
        'balance',
        'state_box',
    ];
    protected $casts = [
        'box_id'=>'integer',
        'open_employee_id'=>'integer',
        'owner_id'=>'integer',
        'close_employee_id'=>'integer',
        'initial_value'=> 'float',
        'ingress'=> 'float',
        'egress'=> 'float',
        'balance' => 'float',
    ];
}