<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {

            // Regenerar la sesión para prevenir fijación de sesión
            $request->session()->regenerate();

            $user = auth()->user();
            $tenant = $user->tenants()->first();

            if ($tenant) {
                // Establecer current_tenant_id
                $user->update(['current_tenant_id' => $tenant->id]);
                // Initialize the tenant context
                // tenancy()->initialize($tenant);

                // Redirect to the tenant's dashboard (using a named route)
                // Redirigir al subdominio del tenant
                $tenantDomain = $tenant->id . '.' . config('tenancy.central_domains')[1];
                // return redirect()->intended("http://{$tenantDomain}/dashboard");
                return Inertia::location("http://{$tenantDomain}"); // Ensure this route exists
                // return redirect()->route('tenant.dashboard', $tenant->id); // Ensure this route exists
            } else {
                return redirect()->route('dashboard');
            }
        }

        return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        // Cerrar sesión del usuario
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Redirigir al dominio del tenant
        // $domain = config('tenancy.central_domains')[1];
        // return Inertia::location("http://{$domain}"); // Ensure this route exists


        // Redirigir al usuario a la página de login principal (base de datos central)
        return redirect()->away('http://localhost/login');
    }
}
