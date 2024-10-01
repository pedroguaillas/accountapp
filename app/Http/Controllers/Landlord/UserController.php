<?php

namespace App\Http\Controllers\Landlord;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('User/Index', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $inputs = [...$request->all(), 'password' => Hash::make($request->password)];
        User::create($inputs);
    }
}
