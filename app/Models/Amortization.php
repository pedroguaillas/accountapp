<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    use HasFactory;

    protected $fillable = [
        'intangible_asset_id',
        'date',
        'percentage',
        'amount',
        'value',
        'accumulated',
    ];
}
