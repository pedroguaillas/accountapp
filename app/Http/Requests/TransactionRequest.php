<?php

namespace App\Http\Requests;

use App\Models\MovementType;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class TransactionRequest extends FormRequest
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
            'transaction_date' => "required|date|before_or_equal:$date",
            'payment_method' => 'required',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'beneficiary_id' => 'required|exists:people,id',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $bankAccount = BankAccount::find($this->bank_account_id);
            $movementType = MovementType::find($this->movement_type_id);

            if (!$movementType) {
                $validator->errors()->add('movement_type_id', "Seleccionar movimiento");
            }

            if (!$bankAccount) {
                $validator->errors()->add('bank_account_id', "Seleccione la cuenta bancaria");
            }

            if ($movementType && $bankAccount && $movementType->type === 'Egreso' && $this->amount > $bankAccount->current_balance) {
                $validator->errors()->add('amount', "El monto no puede ser mayor que el saldo disponible: $bankAccount->current_balance");
            }
        });
    }
}