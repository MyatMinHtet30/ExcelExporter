<?php

namespace App\Http\Controllers;

use App\Http\Requests\CondoStoreRequest;
use App\Http\Requests\CondoUpdateRequest;
use App\Models\Condo;
use App\Models\CondoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class CondoController extends Controller
{
    // GET /condos
    public function index(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $q = $request->get('q');

        $condos = Condo::query()
            ->with(['details']) // <- important to avoid N+1
            ->when($q, fn ($qq) => $qq
                ->where('quotation_number', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->orWhere('job_name', 'like', "%{$q}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.condo', compact('condos', 'q'));
    }

    // GET /condos/create
    public function create()
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        // Your create form: resources/views/pages/createcondo.blade.php
        return view('pages.createcondo'); // your Blade you pasted
    }

    // POST /condos (header + details in one submit)
    public function store(CondoStoreRequest $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $data  = $request->validated();
        $items = $data['items'] ?? [];   // optional details

        $payload = [
            'customer_name'    => $data['customer_name'],
            'address'          => $data['address'] ?? null,
            'job_name'         => $data['job_name'],
            'quotation_number' => $data['quotation_number'],
            'quotation_date'   => $data['quotation_date'],         // map
            'payment_term'     => $data['payment_term'] ?? null,   // map
            'credits'           => $data['credits'] ?? null,        // map
            'status'           => true,
        ];

        $condo = DB::transaction(function () use ($payload, $items) {
            /** @var \App\Models\Condo $condo */
            $condo = Condo::create($payload);

            if (!empty($items)) {
                $rows = array_map(function ($row, $i) {
                    return [
                        'no'                   => $row['no'] ?? ($i + 1),
                        'details'              => $row['details'] ?? null,
                        'amount'               => $row['amount'] ?? null,
                        'unit'                 => $row['unit'] ?? null,
                        'material_cost'        => $row['material_cost'] ?? null,
                        'labor_cost'           => $row['labor_cost'] ?? null,
                        'price_per_unit_total' => $row['price_per_unit_total'] ?? null,
                        'status'               => true,
                    ];
                }, $items, array_keys($items));

                $condo->details()->createMany($rows);
            }

            return $condo;
        });

        // go to edit page so user can add/adjust details
        return redirect()
            ->route('condo', $condo)
            ->with('success', 'Quotation created successfully.');
    }

    // GET /condos/{condo}
    public function show(Condo $condo)
    {
        $condo->load(['details' => fn($q) => $q->orderBy('no')]);
        return view('condos.show', compact('condo'));
    }

    // GET /condos/{condo}/edit
    public function edit(Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $condo->load(['details' => fn($q) => $q->orderBy('no')]);

        // Your edit page: resources/views/pages/condoedit.blade.php (create this)
        return view('pages.editcondo', compact('condo'));
    }

    // POST /condos/{condo}/update  (your POST-style update route)
    public function update(CondoUpdateRequest $request, Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $data  = $request->validated();
        // Keep items even if some fields fail strict casting
        $items = $request->input('items', []);

        $payload = [
            'customer_name'    => $data['customer_name']    ?? $condo->customer_name,
            'address'          => $data['address']          ?? $condo->address,
            'job_name'         => $data['job_name']         ?? $condo->job_name,
            'quotation_number' => $data['quotation_number'] ?? $condo->quotation_number,
            'quotation_date'   => $data['quotation_date']   ?? optional($condo->quotation_date)->format('Y-m-d'),
            'payment_term'     => $data['payment_term']     ?? $condo->payment_term,
            'credits'          => $data['credits']          ?? $condo->credits,
            'status'           => $data['status']           ?? $condo->status,
        ];

        DB::transaction(function () use ($condo, $payload, $items) {
            $condo->update($payload);

            // Upsert details:
            $keepIds = [];

            foreach ($items as $i => $row) {
                // detect empty rows (ignore)
                $hasAny = false;
                foreach (['details','amount','unit','material_cost','labor_cost','price_per_unit_total'] as $k) {
                    if (isset($row[$k]) && trim((string)$row[$k]) !== '') { $hasAny = true; break; }
                }
                if (! $hasAny) continue;

                $attrs = [
                    'no'                   => $row['no'] ?? ($i + 1),
                    'details'              => $row['details'] ?? null,
                    'amount'               => $row['amount'] ?? null,
                    'unit'                 => $row['unit'] ?? null,
                    'material_cost'        => $row['material_cost'] ?? null,
                    'labor_cost'           => $row['labor_cost'] ?? null,
                    'price_per_unit_total' => $row['price_per_unit_total'] ?? null,
                    'status'               => true,
                ];

                if (!empty($row['id'])) {
                    // update existing
                    $detail = $condo->details()->whereKey($row['id'])->first();
                    if ($detail) {
                        $detail->update($attrs);
                        $keepIds[] = $detail->id;
                    }
                } else {
                    // create new
                    $detail = $condo->details()->create($attrs);
                    $keepIds[] = $detail->id;
                }
            }

            // delete rows user actually removed from the form
            if (count($keepIds) > 0) {
                $condo->details()->whereNotIn('id', $keepIds)->delete();
            } else {

            }
        });

        return redirect()->route('condo')->with('success', 'Condo updated.');
    }



    // DELETE /condos/{condo}
    public function destroy(Condo $condo)
    {
        DB::transaction(function () use ($condo) {
            // FK is cascadeOnDelete; this is just explicit if needed
            $condo->details()->delete();
            $condo->delete();
        });

        return redirect()->route('condo')->with('success', 'Condo deleted.');
    }

    // ---------- helper ----------
    private function computeTotals(array $items): array
    {
        $subtotals = collect($items)->map(function ($r) {
            $amount   = (float)($r['amount'] ?? 0);
            $ppu      = (float)($r['price_per_unit_total'] ?? 0);
            $material = (float)($r['material_cost'] ?? 0);
            $labor    = (float)($r['labor_cost'] ?? 0);

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

        return [$total, $vat, $grand];
    }
}
