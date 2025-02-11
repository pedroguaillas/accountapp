<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BankAccount;

class TransactionStoreRequest extends FormRequest
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
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            // 'amount' => [
            //     'required',
            //     'numeric',
            //     'min:0.01',
            //     function ($attribute, $value, $fail) {
            //         // Obtener la cuenta bancaria usando el ID del request
            //         $bankAccount = BankAccount::find($this->input('bank_account_id'));

            //         // Validar si el monto supera el saldo disponible
            //         if ($bankAccount && $value > $bankAccount->current_balance) {
            //             $fail("El monto no puede ser mayor que el saldo disponible: {$bankAccount->current_balance}");
            //         }
            //     },
            // ],
        ];
    }
}
