<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        // Logica para ver la relacion usuario tenant por el role
        // ..............

        $path = 'pes';

        // Find the tenant using the subdomain
        $tenant = Tenant::where('id', $path)->first();

        if (!$tenant) {
            return redirect()->back()->withErrors(['tenant' => 'Invalid tenant subdomain']);
        }

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Initialize the tenant context
            tenancy()->initialize($tenant);

            // Redirect to the tenant's dashboard (using a named route)
            return redirect()->route('tenant.dashboard', $path); // Ensure this route exists
            // return response()->json(['subdomain' => $tenantSubdomain]);
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
    }
}
