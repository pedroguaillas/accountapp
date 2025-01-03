<?php

namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\PaymentRole;
use App\Models\PaymentRoleIngress;
use App\Models\PaymentRoleEgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;


class PaymentRoleController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now();
        $search = $request->input('search', '');
        $year = $request->input('year', $date->year);
        //dd($year);
        $month = $request->input('month', $date->month);
        $company = Company::first();

       // $job = new ProcessPaymenRole();
        //$job->handle();

        // Consulta principal con relaciones
        $paymentroles = PaymentRole::with(['employee', 'paymentroleingresses.roleIngress', 'paymentroleegresses.roleEgress'])
            ->when($search, function ($query, $search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                    ->whereNull('deleted_at');
            })
            ->where('company_id', $company->id) // Filtrar por compañía si es necesario
            ->whereRaw("EXTRACT(YEAR FROM \"date\") = ?", [$year]) // Filtrar por año
            ->whereRaw("EXTRACT(MONTH FROM \"date\") = ?", [$month]) // Filtrar por mes
            ->whereNull('deleted_at')
            ->paginate(10)
            ->withQueryString()

            ->through(function ($paymentRole) {
                $totalIngressF = $paymentRole->paymentroleingresses->filter(function ($ingress) {
                    return $ingress->roleIngress && $ingress->roleIngress->type === 'fijo'; // Verifica que roleIngress no sea null
                })->sum('value') ?? 0;

                $totalIngressO = $paymentRole->paymentroleingresses->filter(function ($ingress) {
                    return $ingress->roleIngress && $ingress->roleIngress->type === 'otro'; // Verifica que roleIngress no sea null
                })->sum('value') ?? 0;

                // Sumar los egresos según el tipo definido en RoleEgress
                $totalEgressF = $paymentRole->paymentroleegresses->filter(function ($egress) {
                    return $egress->roleEgress && $egress->roleEgress->type === 'fijo'; // Verifica que roleEgress no sea null
                })->sum('value') ?? 0;

                $totalEgressO = $paymentRole->paymentroleegresses->filter(function ($egress) {
                    return $egress->roleEgress && $egress->roleEgress->type === 'otro'; // Verifica que roleEgress no sea null
                })->sum('value') ?? 0;

                return [
                    'id' => $paymentRole->id,
                    'cuit' => $paymentRole->employee->cuit,
                    'name' => $paymentRole->employee->name,
                    'sector_code' => $paymentRole->employee->sector_code,
                    'days' => $paymentRole->employee->days,
                    'position' => $paymentRole->employee->position,
                    'salary' => $paymentRole->employee->salary,
                    'total_ingress_f' => $totalIngressF,
                    'total_ingress_o' => $totalIngressO,
                    'total_egress_f' => $totalEgressF,
                    'total_egress_o' => $totalEgressO,
                    'salary_receive' => $paymentRole->salary_receive,
                    'ingresses' => $paymentRole->paymentroleingresses->map(function ($ingress) {
                        return [
                            'id' => $ingress->id,
                            'name' => $ingress->roleIngress->name,
                            'role_ingress_id' => $ingress->role_ingress_id,
                            'value' => $ingress->value,
                        ];
                    }),
                    'egresses' => $paymentRole->paymentroleegresses->map(function ($egress) {
                        return [
                            'id' => $egress->id,
                            'name' => $egress->roleEgress->name,
                            'role_egress_id' => $egress->role_egress_id,
                            'value' => $egress->value,
                        ];
                    }),
                ];
            });

        //dd($paymentroles);

        return inertia('PaymentRol/Index', [
            'filters' => [
                'search' => $search,
                'year' => $year,
                'month' => $month,
            ],
            'paymentroles' => $paymentroles, // Datos de roles paginados con vectores
        ]);
    }

    // PaymentRoleController.php
    public function update(Request $request, $id)
    {
        // Encuentra el PaymentRole por su ID
        $paymentRole = PaymentRole::findOrFail($id);

        // Actualiza los valores de los ingresos
        foreach ($request->input('ingresses') as $ingress) {
            $paymentRoleIngress = PaymentRoleIngress::findOrFail($ingress['id']);
            $paymentRoleIngress->value = $ingress['value'];
            $paymentRoleIngress->save();
        }

        // Actualiza los valores de los egresos
        foreach ($request->input('egresses') as $egress) {
            $paymentRoleEgress = PaymentRoleEgress::findOrFail($egress['id']);
            $paymentRoleEgress->value = $egress['value'];
            $paymentRoleEgress->save();
        }

        // Responde con los datos actualizados o un mensaje de éxito
        return response()->json([
            'success' => true,
            'message' => 'Pago actualizado correctamente',
            'paymentRole' => $paymentRole->load(['paymentroleingresses', 'paymentroleegresses']),
        ]);
    }


}
