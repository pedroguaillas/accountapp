<?php

namespace App\Jobs;

use App\Models\PaymentRole;
use App\Models\Employee;
use App\Models\Company;
use App\Models\RoleIngress;
use App\Models\RoleEgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;

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

        //poner un where para aportacion > cero
        $employees = Employee::where('company_id', $company->id)->get();

        $roleingresses = RoleIngress::where('company_id', $company->id)->get();

        $roleegresses = RoleEgress::where('company_id', $company->id)->get();

        foreach ($employees as $employee) {

            // Crear el rol de pago
            $paymentRole = PaymentRole::create([
                'company_id' => $company->id,
                'employee_id' => $employee->id,
                'position' => $employee->position,
                'sector_code' => $employee->sector_code,
                'days' => $employee->days,
                'salary' => $employee->salary,
                'salary_receive' => $employee->salary, // Inicializamos con el salario base
                'date' => Carbon::now()->format('Y-m-d'), // Fecha dinámica
            ]);


            // Calcular horas ordinarias, horas extras y adelanto
            $ho = $this->calcularHorasOrdinarias($employee);
            $he = $this->calcularHorasExtras($employee);
            $au = $this->calcularAdelanto($employee);
            $as = $this->calcularAdelantoS($employee);
            $remuneration = $employee->salary + $ho + $he;

            // Ingresos (para todos los roles de ingresos)
            $inputpaymentroleingress = [];
            $totalIngresos = 0;  // Para sumar los ingresos
            foreach ($roleingresses as $roleingress) {
                $call = $this->calcularIngreso($roleingress, $employee, $ho, $he, $remuneration, $au);
                $totalIngresos += $call;  // Acumulamos el total de ingresos
                $inputpaymentroleingress[] = [
                    'company_id' => $company->id,
                    'role_ingress_id' => $roleingress->id,
                    'value' => $call,
                ];
            }

            // Egresos (para todos los roles de egresos)
            $inputpaymentroleegress = [];
            $totalEgresos = 0;  // Para sumar los egresos
            foreach ($roleegresses as $roleegress) {
                $call = $this->calcularEgreso($roleegress, $remuneration, $as);
                $totalEgresos += $call;  // Acumulamos el total de egresos
                $inputpaymentroleegress[] = [
                    'company_id' => $company->id,
                    'role_egress_id' => $roleegress->id,
                    'value' => $call,
                ];
            }

            // Calcular salary_receive
            $salaryReceive = $totalIngresos - $totalEgresos;

            // Actualizar el campo 'salary_receive' en el PaymentRole
            $paymentRole->update([
                'salary_receive' => $salaryReceive
            ]);
            // Crear las relaciones de ingresos y egresos para el rol de pago
            $paymentRole->paymentroleingresses()->createMany($inputpaymentroleingress);
            $paymentRole->paymentroleegresses()->createMany($inputpaymentroleegress);

        }

    }


    private function calcularIngreso($roleingress, $employee, $ho, $he, $remuneration, $au)
    {
        switch ($roleingress->code) {
            case 'HO':
                return $ho;
            case 'HE':
                return $he;
            case 'RE':
                return $remuneration;
            case 'XIII':
                return $employee->xiii ? 0 : $remuneration / 12;
            case 'XV':
                return $employee->xiv ? 0 : (38.33 * 30) / $employee->days;
            case 'FDR':
                return $employee->reserve_funds ? 0 : $remuneration * 0.083333;
            case 'AU':
                return $au;
            default:
                return 0;
        }
    }



    private function calcularEgreso($roleegress, $remuneration, $as)
    {
        switch ($roleegress->code) {
            case 'IESSPA':
                return $remuneration * 0.0935; // ejemplo
            case 'IESSPE':
                return $remuneration * 0.0935; // ejemplo
            case 'AS':
                return $as; // ejemplo
            default:
                return 0;
        }
    }

    private function calcularHorasOrdinarias($employee)
    {
        // Lógica para calcular horas ordinarias
        return 0; // Ejemplo
    }

    private function calcularHorasExtras($employee)
    {
        // Lógica para calcular horas extras
        return 0; // Ejemplo
    }

    private function calcularAdelanto($employee)
    {
        // Lógica para calcular adelanto
        return 0; // Ejemplo
    }

    private function calcularAdelantoS($employee)
    {
        // Lógica para calcular adelanto
        return 0; // Ejemplo
    }
}
