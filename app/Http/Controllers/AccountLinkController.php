<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountLinkController extends Controller
{
    public function roles()
    {

        return inertia('Business/AccountLink/Index');
    }
}
