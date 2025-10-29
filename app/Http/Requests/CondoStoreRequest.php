<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CondoStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // header
            'customer_name'    => ['required','string','max:255'],
            'address'          => ['nullable','string','max:1000'],
            'job_name'         => ['required','string','max:255'],
            'quotation_number' => ['required','string','max:100','unique:condos,quotation_number'],
            'quotation_date'   => ['required','date'],
            'payment_term'     => ['nullable','string','max:255'],
            'credits'          => ['nullable','string','max:255'],

            // details (optional on create; if present, validate)
            'items'                        => ['nullable','array','min:1'],
            'items.*.no'                   => ['nullable','integer','min:1'],
            'items.*.details'              => ['required_with:items','string','max:1000'],
            'items.*.amount'               => ['required_with:items','numeric','min:0'],
            'items.*.unit'                 => ['nullable','string','max:50'],
            'items.*.material_cost'        => ['nullable','numeric','min:0'],
            'items.*.labor_cost'           => ['nullable','numeric','min:0'],
            'items.*.price_per_unit_total' => ['nullable','numeric','min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', null);

        if (is_array($items)) {
            // 1) keep only rows that have a non-empty "details"
            $items = array_values(array_filter($items, function ($r) {
                return is_array($r) && isset($r['details']) && trim((string)$r['details']) !== '';
            }));

            // 2) strip commas from numeric fields so numeric rule passes
            $numericFields = ['amount','material_cost','labor_cost','price_per_unit_total'];
            foreach ($items as $i => $row) {
                foreach ($numericFields as $f) {
                    if (isset($row[$f]) && $row[$f] !== '') {
                        $items[$i][$f] = str_replace(',', '', (string)$row[$f]);
                    }
                }
            }
        }

        $this->merge(['items' => $items]);
    }
}
