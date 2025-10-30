<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condo extends Model
{
    protected $fillable = [
        'customer_name','address','job_name',
        'quotation_number','quotation_date',
        'payment_term','credits',
        'status',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'total' => 'decimal:2',
        'vat' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(CondoDetail::class);
    }

    protected $appends = ['computed_subtotal','computed_vat','computed_grand_total'];

    public function getComputedSubtotalAttribute(): float
    {
        $sum = 0.0;
        foreach ($this->details as $r) {
            $amount   = (float)($r->amount ?? 0);
            $ppu      = (float)($r->price_per_unit_total ?? 0);
            $material = (float)($r->material_cost ?? 0);
            $labor    = (float)($r->labor_cost ?? 0);

            if ($amount > 0 && $ppu > 0) {
                $sum += $amount * $ppu;
            } elseif ($material > 0 || $labor > 0) {
                $sum += ($material + $labor);
            }
        }
        return round($sum, 2);
    }

    public function getComputedVatAttribute(): float
    {
        return round($this->computed_subtotal * 0.07, 2);
    }

    public function getComputedGrandTotalAttribute(): float
    {
        return round($this->computed_subtotal + $this->computed_vat, 2);
    }
}
