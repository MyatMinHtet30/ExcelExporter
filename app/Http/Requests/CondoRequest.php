<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\ValidationMessage;

class CondoRequest extends FormRequest
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
            'customer_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'job_name' => 'required|string',
            'quotation_number' => 'required|string',
            'date' => 'required|date',
            'payment_terms' => 'required|string',
            'credit' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'customer_name.required' => 'Customer name ' . ValidationMessage::$required,
            'customer_name.string' => 'Customer name ' . ValidationMessage::$string,
            'customer_name.maxlength' => 'Customer name ' . ValidationMessage::$maxLength,

            'address.required' => 'Address ' . ValidationMessage::$required,
            
        ];
    }
}
