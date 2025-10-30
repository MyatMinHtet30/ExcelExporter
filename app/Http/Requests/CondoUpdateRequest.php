<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CondoUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);

        foreach ($items as $k => $row) {
            foreach (['amount','material_cost','labor_cost','price_per_unit_total'] as $f) {
                if (array_key_exists($f, $row)) {
                    // remove commas/spaces so "11,111.00" => "11111.00"
                    $val = preg_replace('/[,\s]/', '', (string)$row[$f]);
                    $row[$f] = $val === '' ? null : $val;
                }
            }
            $items[$k] = $row;
        }

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        // If route model binding gives a Condo model, normalize to its id:
        $condoParam = $this->route('condo');
        $condoId = is_object($condoParam) ? $condoParam->getKey() : $condoParam;

        return [
            // Header fields
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
            'status'           => ['nullable','boolean'],

            // ✅ Items array (keep it loose so empty rows don't fail the whole form)
            'items'                                => ['array'],
            'items.*.id'                           => ['nullable','integer','exists:condo_details,id'],
            'items.*.no'                           => ['nullable','integer','min:1'],
            'items.*.details'                      => ['nullable','string','max:1000'],
            'items.*.amount'                       => ['nullable','numeric'],
            'items.*.unit'                         => ['nullable','string','max:50'],
            'items.*.material_cost'                => ['nullable','numeric'],
            'items.*.labor_cost'                   => ['nullable','numeric'],
            'items.*.price_per_unit_total'         => ['nullable','numeric'],
        ];
    }
}
