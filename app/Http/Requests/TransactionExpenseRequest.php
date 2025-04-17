<?php

namespace App\Http\Requests;

use App\Models\BankAccount;
use App\Models\CashSession;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class TransactionExpenseRequest extends FormRequest
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
            'date_expense' => "required|date|before_or_equal:$date",
            'payment_method_id' => "required",
            'payment_type' => "required",
            'expense_id'=> "required",
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
            if ($this->payment_type === 'caja') {

                $cash = CashSession::where('id', $this->payment_method_id)->first();

                if ($this->amount > $cash->balance) {
                    $validator->errors()->add('amount', "El monto no puede ser mayor al saldo de caja: $cash->balance");
                }

            } else {
                if ($this->payment_type === 'banco') {//debe ser caja o banco por ende se hace las dos comparaciones sino asume directo
                    $bank = BankAccount::where('id', $this->payment_method_id)->first();

                    if ($this->amount > $bank->current_balance) {
                        $validator->errors()->add('amount', "El monto no puede ser mayor al saldo de banco: $bank->current_balance");
                    }
                }

            }

        });
    }
}
