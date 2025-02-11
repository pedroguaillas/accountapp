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
        'data_additional',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'data_additional' => 'array',
            'percentage' => 'float',
            'amount' => 'float',
            'accumulated' => 'float',
        ];
    }
}
