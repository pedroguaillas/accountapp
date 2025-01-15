<?php

namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\Journal;
use App\Models\PaymentRole;
use App\Models\PaymentRoleIngress;
use App\Models\PaymentRoleEgress;
use App\Jobs\ProcessPaymenRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Exports\PaymentRolesExport;
use Maatwebsite\Excel\Facades\Excel;


class PaymentRoleController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now(); // Fecha actual
        if (!$date->isLastOfMonth()) { // Verifica si no es el último día del mes
            $date = $date->subMonth(); // Resta un mes
        }
        $search = $request->input('search', '');
        $year = $request->input('year', $date->year);
        $month = $request->input('month', $date->month);
        $company = Company::first();


        //$job = new ProcessPaymenRole();
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
                    'state' => $paymentRole->state,
                    'ingresses' => $paymentRole->paymentroleingresses
                        ->filter(function ($ingress) {
                            return !in_array($ingress->roleIngress->code, ['OI']);
                        })
                        ->map(function ($ingress) {
                            return [
                                'id' => $ingress->id,
                                'name' => $ingress->roleIngress->name,
                                'role_ingress_id' => $ingress->role_ingress_id,
                                'value' => $ingress->value,
                            ];
                        }),

                    'egresses' => $paymentRole->paymentroleegresses
                        ->filter(function ($egress) {
                            return !in_array($egress->roleEgress->code, ['OE', 'SP']);
                        })
                        ->map(function ($egress) {
                            return [
                                'id' => $egress->id,
                                'name' => $egress->roleEgress->name,
                                'role_egress_id' => $egress->role_egress_id,
                                'value' => $egress->value,
                            ];
                        }),

                ];
            });
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

    public function export(Request $request)
    {

        $search = $request->input('search');
        $year = $request->input('year');
        $month = $request->input('month');

        // Pasar los filtros al exportador
        return Excel::download(new PaymentRolesExport($search, $year, $month), 'payment_roles.xlsx');
    }

    public function generate(Request $request)
    {

        $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')->first();
        if (!$journal) {
            return response()->json(['error' => "Debe generar el ASIENTO DE SITUACION INICIAL"], 412);
        }

        $rolesIds = $request->selectedIds;
        $company = Company::first();
        $user = auth()->user();
        $date = Carbon::now();

        $paymentroles = PaymentRole::with(['employee', 'paymentroleingresses.roleIngress', 'paymentroleegresses.roleEgress'])
            ->whereIn('payment_roles.id', $rolesIds)->where('state', 'CREADO')->get(); // Obtén los resultados como una colección
        // Mapea los resultados para transformarlos
        $paymentroles->map(function ($paymentRole) use ($company, $user, $date) {
            // Mapear los ingresos
            // Mapear los ingresos (solo de tipo Fijo)
            $ingressData = $paymentRole->paymentroleingresses
                ->filter(function ($ingress) {
                    // Acceder a 'type' desde roleIngress
                    return $ingress->roleIngress->type === 'fijo';
                })
                ->mapWithKeys(function ($ingress) {
                    return [
                        $ingress->roleIngress->id => [
                            'account_spent_id' => $ingress->roleIngress->account_spent_id,
                            'account_active_id' => $ingress->roleIngress->account_active_id,
                            'account_pasive_id' => $ingress->roleIngress->account_pasive_id,
                            'amount' => $ingress->value,
                            'code' => $ingress->roleIngress->code,
                        ]
                    ];
                });

            $otrosIngressSum = $paymentRole->paymentroleingresses
                ->filter(function ($ingress) {
                    return $ingress->roleIngress->type === 'otro';// Asegúrate de que 'type' sea el campo correcto en tu modelo
                })
                ->sum(function ($ingress) {
                    // Convierte 'amount' a float
                    return floatval($ingress->value);
                });
            ;


            // Mapear los egresos (solo de tipo Fijo)
            $egressData = $paymentRole->paymentroleegresses
                ->filter(function ($egress) {
                    return $egress->roleEgress->type === 'fijo';  // Ajusta el campo 'type' según tu modelo
                })
                ->mapWithKeys(function ($egress) {
                    return [
                        $egress->roleEgress->id => [
                            'account_spent_id' => $egress->roleEgress->account_spent_id,
                            'account_active_id' => $egress->roleEgress->account_active_id,
                            'account_pasive_id' => $egress->roleEgress->account_pasive_id,
                            'amount' => $egress->value,
                            'code' => $egress->roleEgress->code,
                        ]
                    ];
                });

            $otrosEgressSum = $paymentRole->paymentroleegresses
                ->filter(function ($egress) {
                    return $egress->roleEgress->type === 'otro'; // Asegúrate de que 'type' sea el campo correcto en tu modelo
                })
                ->sum(function ($egress) {
                    // Convierte 'amount' a float
                    return floatval($egress->value);
                });

            $sumDebit = 0;

            // Crear el diario
            $inputs = [
                'description' => "Rol de pagos " . $paymentRole->employee->cuit . " " . $paymentRole->employee->name,
                'date' => $date,
                'user_id' => $user->id,
            ];
            // Crear las entradas en el diario
            $journalEntries = [];

            foreach ($ingressData as $account_spent_id => $value) {
                if ($value['amount'] != 0) {
                    $journalEntries[] = [
                        'account_id' => $value['code'] === 'AU' ? $value['account_active_id'] : $value['account_spent_id'],
                        'debit' => $value['amount'],
                        'have' => 0,
                    ];
                    $sumDebit += $value['amount'];
                }
            }

            foreach ($ingressData as $account_spent_id => $value) {
                if (($value['code'] === 'OI') && !($otrosIngressSum == 0)) {
                    $journalEntries[] = [
                        'account_id' => $value['account_spent_id'],
                        'debit' => $otrosIngressSum,
                        'have' => 0,
                    ];
                    $sumDebit += $otrosIngressSum;
                }
            }

            $sumHave = 0;
            $validCodes = ['XIII', 'XIV', 'FDR', 'VC', 'AL']; // Lista de códigos válidos

            foreach ($ingressData as $account_pasive_id => $value) {
                if (in_array($value['code'], $validCodes) && $value['amount'] != 0) {
                    $journalEntries[] = [
                        'account_id' => $value['account_pasive_id'],
                        'debit' => 0,
                        'have' => $value['amount'],
                    ];
                    $sumHave += $value['amount'];
                }
            }

            // Procesar los egresos
            foreach ($egressData as $account_pasive_id => $value) {
                if ((!($value['code'] == 'SP') or !($value['code'] == 'OE')) && !($value['amount'] == 0)) {
                    $journalEntries[] = [
                        'account_id' => $value['account_pasive_id'],
                        'debit' => 0,
                        'have' => $value['amount'],
                    ];
                    $sumHave += $value['amount'];
                }
            }

            foreach ($egressData as $account_pasive_id => $value) {
                if (($value['code'] == 'OE') && !($otrosEgressSum == 0)) {
                    $journalEntries[] = [
                        'account_id' => $value['account_pasive_id'],
                        'debit' => 0,
                        'have' => $otrosEgressSum,
                    ];
                    $sumHave += $otrosEgressSum;
                }
            }

            $salary = $sumDebit - $sumHave;
            foreach ($egressData as $account_pasive_id => $value) {
                if ($value['code'] == 'SP') {
                    $journalEntries[] = [
                        'account_id' => $value['account_pasive_id'],
                        'debit' => 0,
                        'have' => $salary,
                    ];
                }
            }

            // Guardar las entradas en el diario
            $journal = $company->journals()->create($inputs);
            $journal->journalentries()->createMany($journalEntries);
            $paymentRole->update(['state' => 'GENERADO']);
        });
    }
}
