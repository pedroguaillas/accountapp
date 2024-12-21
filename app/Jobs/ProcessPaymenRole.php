<?php

namespace App\Jobs;

use App\Models\PaymentRole;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymenRole implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public PaymentRole $paymentRole)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employees = Employee::all(); // Obtén todos los empleados
        foreach ($employees as $employee) { // Itera correctamente
            $this->paymentRole->create([
                'company_id' => 1,
                'employee_id' => $employee->id,
                'position' => $employee->position,
                'sector_code' => $employee->sector_code,
                'days' => $employee->days,
                'salary' => $employee->salary,
                'salary_receive' => $employee->salary,
                'date'=>"20-12-2024",
            ]);
            //$this->paymentRoleIngress->create(['company_id'=>1,'payment_role_id'=>1,'role_ingress_id'=>1,'value'=>50]);
        }
    }
}
