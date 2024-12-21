<?php

namespace App\Jobs;

use App\Models\PaymentRole;
use App\Models\PaymentRoleIngress;
use App\Models\Employee;
use App\Models\Company;
use App\Models\RoleIngress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon; // Importa la clase Carbon

class ProcessPaymenRole implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $company = Company::first();
        //if (Carbon::now()->isLastOfMonth()) {
        $employees = Employee::where('company_id', $company->id)->get();
        $roleingresses = RoleIngress::where('company_id', $company->id)->get();

        foreach ($employees as $employee) { // Itera correctamente
            $paymentRole = PaymentRole::create([
                'company_id' => $company->id,
                'employee_id' => $employee->id,
                'position' => $employee->position,
                'sector_code' => $employee->sector_code,
                'days' => $employee->days,
                'salary' => $employee->salary,
                'salary_receive' => $employee->salary,
                'date' => "20-12-2024",

            ]);

            //FALTA calculo de horas ordinarias,calculo de horas extras, adelantos como ingrso y egreso fijo ya que ya tenmos la gestion, 
            $ho = 0;
            $he = 0;
            $remuneration = $employee->salary + $ho + $he;
            //FALTA verificar las fechas de pago anuales de XIII, XIV Y FONDOS DE RESERVA y hacer lo mismo para lso egresos
            $inputpaymentroleingress = [];
            foreach ($roleingresses as $roleingress) {
                $call = 0;
                if ($roleingress->code === 'HO') {
                    $call = $ho;
                }
                if ($roleingress->code === 'HE') {
                    $call = $he;
                }
                if ($roleingress->code === 'RE') {
                    $call = $remuneration;
                }
                if ($roleingress->code === 'XIII') {
                    $call = $employee->xiii ? 0 : $remuneration / 12;
                }
                if ($roleingress->code === 'XV') {
                    $call = $employee->xiv ? 0 : (38.33 * 30) / $employee->days;
                }
                if ($roleingress->code === 'FDR') {
                    $call = $employee->reserve_funds ? 0 : $remuneration * 0.083333;
                }
                $inputpaymentroleingress[] = [
                    'company_id' => $company->id,
                    // 'payment_role_id' =>$paymentRole->id ,
                    'role_ingress_id' => $roleingress->id,
                    'value' => $call,
                ];

            }
            $paymentRole->paymentroleingress()->createMany($inputpaymentroleingress);

        }
        // }
    }


}
