<?php

namespace App\Http\Controllers;

use App\Models\Home;
use App\Models\HomeDetail;
use Illuminate\Http\Request;

class HomeDetailController extends Controller
{
    /** List all details for a home */
    public function index(Home $home)
    {
        $details = $home->details()->orderBy('no')->paginate(50);
        return view('pages.home_details.index', compact('home', 'details'));
    }

    /** Store multiple details (expects details[] array) */
    public function store(Request $request, Home $home)
    {
        $data = $request->validate([
            'details'                       => ['required','array','min:1'],
            'details.*.status'              => ['nullable','boolean'],
            'details.*.no'                  => ['nullable','integer','min:1'],
            'details.*.category_name'       => ['nullable','string','max:255'],
            'details.*.amount'              => ['nullable','numeric','min:0'],
            'details.*.unit'                => ['nullable','string','max:50'],
            'details.*.mc_price'            => ['nullable','numeric','min:0'],
            'details.*.lc_price'            => ['nullable','numeric','min:0'],
        ]);

        $rows = collect($data['details'])->map(function ($r, $i) {
            $amount   = (float)($r['amount'] ?? 0);
            $mc       = (float)($r['mc_price'] ?? 0);
            $lc       = (float)($r['lc_price'] ?? 0);
            $matTotal = round($amount * $mc, 2);
            $labTotal = round($amount * $lc, 2);
            $grand    = round($matTotal + $labTotal, 2);

            return [
                'status'         => (bool)($r['status'] ?? true),
                'no'             => $r['no'] ?? ($i + 1),
                'category_name'  => $r['category_name'] ?? null,
                'amount'         => $amount ?: null,
                'unit'           => $r['unit'] ?? null,
                'mc_price'       => $mc ?: null,
                'lc_price'       => $lc ?: null,
                'material_total' => $amount ? $matTotal : null,
                'labor_total'    => $amount ? $labTotal : null,
                'grand_total'    => ($amount && ($mc || $lc)) ? $grand : null,
            ];
        })->all();

        $home->details()->createMany($rows);

        return back()->with('success', __('Line items saved.'));
    }

    /** Edit one detail */
    public function edit(Home $home, HomeDetail $detail)
    {
        $this->assertParent($home, $detail);
        return view('pages.home_details.edit', compact('home', 'detail'));
    }

    /** Update one detail */
    public function update(Request $request, Home $home, HomeDetail $detail)
    {
        $this->assertParent($home, $detail);

        $r = $request->validate([
            'status'        => ['nullable','boolean'],
            'no'            => ['nullable','integer','min:1'],
            'category_name' => ['nullable','string','max:255'],
            'amount'        => ['nullable','numeric','min:0'],
            'unit'          => ['nullable','string','max:50'],
            'mc_price'      => ['nullable','numeric','min:0'],
            'lc_price'      => ['nullable','numeric','min:0'],
        ]);

        $amount   = (float)($r['amount'] ?? 0);
        $mc       = (float)($r['mc_price'] ?? 0);
        $lc       = (float)($r['lc_price'] ?? 0);
        $matTotal = $amount * $mc;
        $labTotal = $amount * $lc;
        $grand    = $matTotal + $labTotal;

        $detail->update([
            'status'         => (bool)($r['status'] ?? $detail->status),
            'no'             => $r['no'] ?? $detail->no,
            'category_name'  => $r['category_name'] ?? $detail->category_name,
            'amount'         => $r['amount'] ?? $detail->amount,
            'unit'           => $r['unit'] ?? $detail->unit,
            'mc_price'       => $r['mc_price'] ?? $detail->mc_price,
            'lc_price'       => $r['lc_price'] ?? $detail->lc_price,
            'material_total' => $amount ? round($matTotal, 2) : null,
            'labor_total'    => $amount ? round($labTotal, 2) : null,
            'grand_total'    => ($amount && ($mc || $lc)) ? round($grand, 2) : null,
        ]);

        return back()->with('success', __('Line item updated.'));
    }

    /** Delete one detail */
    public function destroy(Home $home, HomeDetail $detail)
    {
        $this->assertParent($home, $detail);
        $detail->delete();

        return back()->with('success', __('Line item deleted.'));
    }

    /** Ensure nested binding integrity */
    private function assertParent(Home $home, HomeDetail $detail): void
    {
        if ($detail->home_id !== $home->id) {
            abort(404);
        }
    }
}
