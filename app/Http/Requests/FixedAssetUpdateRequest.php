<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FixedAssetUpdateRequest extends FormRequest
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
            'pay_method_id' => 'required|exists:pay_methods,id',

            'vaucher' => 'nullable|string|max:17',
            'date_acquisition' => 'required|date',
            'detail' => 'required|string|max:300',
            'code' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'period' => 'required|integer|min:0',
            'value' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'date_end' => 'required|date',
        ];
    }
}
