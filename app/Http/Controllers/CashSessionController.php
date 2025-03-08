<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CashSessionController extends Controller
{ public function open(Request $request, Box $box)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
        ]);

        // Asumimos que el empleado que abre la caja se obtiene del usuario autenticado.
        // Ajusta según tu implementación (por ejemplo, auth()->user()->employee_id).
        $openEmployeeId = auth()->user()->employee_id ?? null;

        $initialValue = $request->input('monto');

        DB::table('cash_sessions')->insert([
            'box_id'           => $box->id,
            'open_employee_id'  => $openEmployeeId,
            'close_employee_id' => null,
            'initial_value'     => $initialValue,
            'ingress'           => 0,
            'egress'            => 0,
            'balance'           => $initialValue,
            'state_box'         => 'open',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta correctamente.',
        ]);
    }

    /**
     * Método para cerrar caja.
     */
    public function close(Request $request, Box $box)
    {
        $request->validate([
            'ingress' => 'required|numeric|min:0',
            'egress'  => 'required|numeric|min:0',
        ]);

        // Buscar la caja abierta para este box (último registro con state_box = 'open')
        $cashSession = DB::table('cash_sessions')
            ->where('box_id', $box->id)
            ->where('state_box', 'open')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$cashSession) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta para cerrar.',
            ], 404);
        }

        $ingress = $request->input('ingress');
        $egress  = $request->input('egress');
        $initialValue = $cashSession->initial_value;
        $balance = $initialValue + $ingress - $egress;

        // Asumimos que el empleado que cierra la caja se obtiene del usuario autenticado.
        $closeEmployeeId = auth()->user()->employee_id ?? null;

        DB::table('cash_sessions')
            ->where('id', $cashSession->id)
            ->update([
                'ingress'           => $ingress,
                'egress'            => $egress,
                'balance'           => $balance,
                'close_employee_id' => $closeEmployeeId,
                'state_box'         => 'close',
                'updated_at'        => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja cerrada correctamente.',
        ]);
    }
}