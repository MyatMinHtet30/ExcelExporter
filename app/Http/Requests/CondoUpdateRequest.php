<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CondoUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $condoId = $this->route('condo'); // route-model binding id

        return [
            'customer_name'    => ['required','string','max:255'],
            'address'          => ['nullable','string','max:1000'],
            'job_name'         => ['required','string','max:255'],
            'quotation_number' => [
                'required','string','max:100',
                Rule::unique('condos','quotation_number')->ignore($condoId),
            ],
            'quotation_date'   => ['required','date'],
            'payment_term'     => ['nullable','string','max:255'],
            'credits'          => ['nullable','string','max:255'],
        ];
    }
}
