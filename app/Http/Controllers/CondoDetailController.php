<?php

namespace App\Http\Controllers;

use App\Http\Requests\CondoDetailStoreRequest;
use App\Http\Requests\CondoDetailUpdateRequest;
use App\Models\Condo;
use App\Models\CondoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CondoDetailController extends Controller
{
    // POST /admin/condos/{condo}/details/store
    public function store(CondoDetailStoreRequest $request, Condo $condo)
    {
        $row = $request->validated();
        $detail = $condo->details()->create($row);

        // recompute header totals
        $this->recomputeTotals($condo);

        return back()->with('success','Item added.');
    }

    // POST /admin/condo-details/{detail}/update
    public function update(CondoDetailUpdateRequest $request, CondoDetail $detail)
    {
        $detail->update($request->validated());

        $this->recomputeTotals($detail->condo);

        return back()->with('success','Item updated.');
    }

    // DELETE /admin/condo-details/{detail}/delete
    public function destroy(CondoDetail $detail)
    {
        $condo = $detail->condo;
        $detail->delete();

        $this->recomputeTotals($condo);

        return back()->with('success','Item removed.');
    }

    private function recomputeTotals(Condo $condo): void
    {
        $subtotals = $condo->details()->get()->map(function ($d) {
            $amount   = (float)($d->amount ?? 0);
            $ppu      = (float)($d->price_per_unit_total ?? 0);
            $material = (float)($d->material_cost ?? 0);
            $labor    = (float)($d->labor_cost ?? 0);

            if ($amount > 0 && $ppu > 0) {
                return round($amount * $ppu, 2);
            }
            if ($material > 0 || $labor > 0) {
                return round($material + $labor, 2);
            }
            return 0.0;
        });

        $total = round($subtotals->sum(), 2);
        $vat   = round($total * 0.07, 2);
        $grand = round($total + $vat, 2);

        $condo->update([
            'total'       => $total,
            'vat'         => $vat,
            'grand_total' => $grand,
        ]);
    }
}


