<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixed_asset_id',
        'date',
        'percentage',
        'amount',
        'value',
        'accumulated',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'percentage' => 'float',
            'amount' => 'float',
            'accumulated' => 'float',
        ];
    }
}
