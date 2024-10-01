<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchToCentralDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la aplicación está en modo de inquilino
        $isTenant = tenancy()->initialized;

        var_dump($isTenant);

        // Logic to determine if we should switch to the central database
        if (!$isTenant) {
            // Switch to the central database
            config(['database.connections.central.database' => env('DB_CONNECTION')]);
            // config(['tenancy.database.central_connection' => env('DB_CONNECTION')]);
        }

        return $next($request);
    }
}
