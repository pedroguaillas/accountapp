<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'depresiation_time'];

    protected $casts = ['depresiation_time' => 'integer'];
}
