<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CondoDetailStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no'                   => ['nullable','integer','min:1'],
            'details'              => ['required','string','max:1000'],
            'amount'               => ['required','numeric','min:0'],
            'unit'                 => ['nullable','string','max:50'],
            'material_cost'        => ['nullable','numeric','min:0'],
            'labor_cost'           => ['nullable','numeric','min:0'],
            'price_per_unit_total' => ['nullable','numeric','min:0'],
        ];
    }
}
