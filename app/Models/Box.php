<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Box extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', // ID Compania  backend
        'name',
        'owner_id',
    ];

    public function cashSessions()
    {
        return $this->hasMany(CashSession::class);
    }
}