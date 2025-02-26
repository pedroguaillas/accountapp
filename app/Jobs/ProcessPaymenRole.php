<?php

namespace App\Jobs;

use App\Models\PaymentRole;
use App\Models\Employee;
use App\Models\Hour;
use App\Models\Advance;
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

        $employees = Employee::where('company_id', $company->id)
            ->where('porcent_aportation', '>', 0)
            ->get();

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
            $as = $this->calcularAdelanto($employee);
            $au = $this->calcularAdelantoS($employee);
            $remuneration = $employee->salary + $ho + $he;

            $data = [
                "company_id" => $company->id,
            ];
    

            // Ingresos (para todos los roles de ingresos)
            $inputpaymentroleingress = [];
            $totalIngresos = 0;  // Para sumar los ingresos
            foreach ($roleingresses as $roleingress) {
                $call = $this->calcularIngreso($roleingress, $employee, $ho, $he, $remuneration, $au);
                $totalIngresos += $call;  // Acumulamos el total de ingresos
                $inputpaymentroleingress[] = [
                    'data_additional' => $data,
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
                    'data_additional' => $data,
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
                return $employee->xiii ? 0 : ($remuneration+$ho+$he )/ 12;
            case 'XV':
                return $employee->xiv ? 0 : ($remuneration) / 12;
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
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;


        // Sumar horas ordinarias del mes y año actual
        return Hour::where('employee_id', $employee->id)
            ->where('type', 'normal') // Tipo de hora ordinaria
            ->whereMonth('date', $mesActual)
            ->whereYear('date', $anioActual)
            ->sum('amount');

    }

    private function calcularHorasExtras($employee)
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;

        // Sumar horas extras del mes y año actual
        return Hour::where('employee_id', $employee->id)
            ->where('type', 'extra') // Tipo de hora extra
            ->whereMonth('date', $mesActual)
            ->whereYear('date', $anioActual)
            ->sum('amount');
    }

    private function calcularAdelanto($employee)
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;


        // Sumar adelantos de tipo 'salario' para el empleado en el mes y año actuales
        return Advance::where('employee_id', $employee->id)
            ->where('type', 'salario') // Tipo de adelanto: salario
            ->whereMonth('date', $mesActual)
            ->whereYear('date', $anioActual)
            ->sum('amount');
    }

    private function calcularAdelantoS($employee)
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
      

        // Sumar adelantos de tipo 'utilidad' para el empleado en el mes y año actuales
        return Advance::where('employee_id', $employee->id)
            ->where('type', 'utilidad') // Tipo de adelanto: utilidad
            ->whereMonth('date', $mesActual)
            ->whereYear('date', $anioActual)
            ->sum('amount');
    }


}
