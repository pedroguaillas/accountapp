<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "tenant_id",
        "user_id",
        "role",
    ];
}
