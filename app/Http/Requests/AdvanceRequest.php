<?php

namespace App\Http\Requests;

use App\Models\BankAccount;
use App\Models\CashSession;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Validation\Validator;

class AdvanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $date = Carbon::now()->toDateString(); // Fecha actual

        return [
            'employee_id' => 'required|exists:employees,id',
            'date' => "required|date|before_or_equal:$date",
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $employee = Employee::find($this->employee_id);

            if ($employee) {
                //TODO verrificar que la sumatoria de los adelantos realizados en el mes no supere los dos sueldos caso contrario mostrar el saldo disponible
                $maxAmount = $employee->salary * 2;
                if ($this->amount > $maxAmount) {
                    $validator->errors()->add('amount', "El monto no puede ser mayor a dos salarios: $maxAmount");
                }
            }
        });

        $validator->after(function ($validator) {
            if ($this->payment_type === 'caja') {
                $cash = CashSession::where('id', $this->payment_method_id)->first();
                if ($this->amount > $cash->balance) {
                    $validator->errors()->add('amount', "El monto no puede ser mayor al saldo de caja: $cash->balance");
                }
            }else
            {
                $bank = BankAccount::where('id', $this->payment_method_id)->first();
                if ($this->amount > $bank->current_balance) {
                    $validator->errors()->add('amount', "El monto no puede ser mayor al saldo de banco: $bank->current_balance");
                }  
            }

        });
    }
}
