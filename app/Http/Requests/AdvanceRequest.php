<?php

namespace App\Http\Requests;

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
            'type' => 'required',
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
                $maxAmount = $employee->salary * 2;
                if ($this->amount > $maxAmount) {
                    $validator->errors()->add('amount', "El monto no puede ser mayor a dos salarios: $maxAmount");
                }
            }
        });
    }
}
