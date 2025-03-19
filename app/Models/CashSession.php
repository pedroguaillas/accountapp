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
        'real_balance',
        'cash_difference',
    ];
    protected $casts = [
        'close_employee_id' => 'integer',
        'initial_value' => 'float',
        'ingress' => 'float',
        'egress' => 'float',
        'balance' => 'float',
        'real_balance' => 'float',
        'cash_difference' => 'float',
    ];
}