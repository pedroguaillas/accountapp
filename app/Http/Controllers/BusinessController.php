<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class BusinessController extends Controller
{
    public function roles()
    {
        return inertia('Business/Setting/Role');
    }
}
