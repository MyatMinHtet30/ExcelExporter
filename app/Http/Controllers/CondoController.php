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
            ->with(['details']) 
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

        $prefix = 'K-QT6804-';

        $lastCondo = Condo::where('quotation_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastCondo && $lastCondo->quotation_number) {
            $parts    = explode('-', $lastCondo->quotation_number);
            $lastSeq  = (int) end($parts);   
            $nextNumber = $lastSeq + 1;
        }

        $nextQuotationNumber = $prefix.$nextNumber;

        return view('pages.createcondo', compact('nextQuotationNumber')); 
    }

    // POST /condos (header + details in one submit)
    public function store(CondoStoreRequest $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $data  = $request->validated();
        $items = $data['items'] ?? [];   

        $prefix = 'K-QT6804-';

        $lastCondo = Condo::where('quotation_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastCondo && $lastCondo->quotation_number) {
            $parts    = explode('-', $lastCondo->quotation_number);
            $lastSeq  = (int) end($parts);   
            $nextNumber = $lastSeq + 1;
        }

        $quotationNumber = $prefix.$nextNumber;

        $payload = [
            'customer_name'    => $data['customer_name'],
            'address'          => $data['address'] ?? null,
            'job_name'         => $data['job_name'],
            'quotation_number' => $quotationNumber,  
            'quotation_date'   => now()->toDateString(),      
            'payment_term'     => $data['payment_term'] ?? null,   
            'credits'           => $data['credits'] ?? null,       
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
            ->route('condo')
            ->with('success', __('Saved successfully.'));
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
            'quotation_number' => $condo->quotation_number,
            'quotation_date'   => now()->toDateString(),
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

        return redirect()->route('condo')->with('success', __('Updated successfully.'));

    }



    // DELETE /condos/{condo}
    public function destroy(Condo $condo)
    {
        DB::transaction(function () use ($condo) {
            $condo->details()->delete();
            $condo->delete();
        });

        return redirect()->route('condo')->with('success', __('Deleted successfully.'));

    }

    // ---------- helper ----------
    private function computeTotals(array $items): array
    {
        $subtotals = collect($items)->map(function ($r) {
            $amount   = (float) str_replace([',',' '], '', (string)($r['amount'] ?? 0));
            $ppu      = (float) str_replace([',',' '], '', (string)($r['price_per_unit_total'] ?? 0));
            $material = (float) str_replace([',',' '], '', (string)($r['material_cost'] ?? 0));
            $labor    = (float) str_replace([',',' '], '', (string)($r['labor_cost'] ?? 0));

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

    // app/Http/Controllers/CondoController.php

    public function preview(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $header = $request->only([
            'customer_name',
            'address',
            'job_name',
            'quotation_number',
            'payment_term',
            'credits',
        ]);
        $condoId = $request->input('condo_id');   
        $previewDate = null;

        if ($condoId) {
            $model = Condo::find($condoId);
            if ($model) {
                $previewDate = $model->updated_at ?: $model->created_at;
            }
        }

        if (! $previewDate) {
            // for create page (no condo yet) or if somehow not found
            $previewDate = now();
        }

        $header['quotation_date'] = $previewDate->format('Y-m-d');

        $items = $request->input('items', []);

        // per-row data for the table
        $rows = [];
        foreach ($items as $i => $r) {
            $amount  = (float) str_replace([',',' '], '', (string)($r['amount'] ?? 0));
            $mcPrice = (float) str_replace([',',' '], '', (string)($r['material_cost'] ?? 0));
            $lcPrice = (float) str_replace([',',' '], '', (string)($r['labor_cost'] ?? 0)); 
            $ppuInput = trim((string)($r['price_per_unit_total'] ?? ''));
            $ppu      = $ppuInput !== ''
                ? (float) str_replace([',',' '], '', $ppuInput)
                : ($mcPrice + $lcPrice);

            // use the same rule as computeTotals()
            $subtotal = 0.0;
            if ($amount > 0 && $ppu > 0) {
                $subtotal = round($amount * $ppu, 2);
            } elseif ($mcPrice > 0 || $lcPrice > 0) {
                $subtotal = round($mcPrice + $lcPrice, 2);
            }

            // skip entirely empty lines
            $hasAny = false;
            foreach (['details','amount','unit','material_cost','labor_cost','price_per_unit_total'] as $k) {
                if (isset($r[$k]) && trim((string)$r[$k]) !== '') { $hasAny = true; break; }
            }
            if (! $hasAny) continue;

            $rows[] = [
                'no'       => ($r['no'] ?? ($i + 1)),
                'details'  => $r['details'] ?? '',
                'amount'   => $amount,
                'unit'     => $r['unit'] ?? '',
                'mc_price' => $mcPrice,
                'lc_price' => $lcPrice,
                'ppu'      => $ppu,
                'subtotal' => $subtotal,
            ];
        }

        // totals (reuse your helper)
        [$total, $vat, $grand] = $this->computeTotals($items);

        return view('pages.condopreview', array_merge($header, [
            'rows'  => $rows,
            'total' => $total,
            'vat'   => $vat,
            'grand' => $grand,
            
        ]));
    }

    public function exportPdf(Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $condo->load(['details' => fn($q) => $q->orderBy('no')]);

        // header data for view
        $header = [
            'customer_name'   => $condo->customer_name,
            'address'         => $condo->address,
            'job_name'        => $condo->job_name,
            'quotation_number'=> $condo->quotation_number,
            'quotation_date'  => $condo->quotation_date,
            'payment_term'    => $condo->payment_term,
            'credits'         => $condo->credits,
        ];

        $items = $condo->details->map(function (CondoDetail $d) {
            return [
                'no'                   => $d->no,
                'details'              => $d->details,
                'amount'               => $d->amount,
                'unit'                 => $d->unit,
                'material_cost'        => $d->material_cost,
                'labor_cost'           => $d->labor_cost,
                'price_per_unit_total' => $d->price_per_unit_total,
            ];
        })->toArray();

        $rows = [];
        foreach ($items as $i => $r) {
            $amount  = (float) str_replace([',',' '], '', (string)($r['amount'] ?? 0));
            $mcPrice = (float) str_replace([',',' '], '', (string)($r['material_cost'] ?? 0));
            $lcPrice = (float) str_replace([',',' '], '', (string)($r['labor_cost'] ?? 0));
            $ppuInput = trim((string)($r['price_per_unit_total'] ?? ''));
            $ppu      = $ppuInput !== ''
                ? (float) str_replace([',',' '], '', $ppuInput)
                : ($mcPrice + $lcPrice);

            $subtotal = 0.0;
            if ($amount > 0 && $ppu > 0) {
                $subtotal = round($amount * $ppu, 2);
            } elseif ($mcPrice > 0 || $lcPrice > 0) {
                $subtotal = round($mcPrice + $lcPrice, 2);
            }

            $hasAny = false;
            foreach (['details','amount','unit','material_cost','labor_cost','price_per_unit_total'] as $k) {
                if (isset($r[$k]) && trim((string)$r[$k]) !== '') { $hasAny = true; break; }
            }
            if (! $hasAny) continue;

            $rows[] = [
                'no'       => ($r['no'] ?? ($i + 1)),
                'details'  => $r['details'] ?? '',
                'amount'   => $amount,
                'unit'     => $r['unit'] ?? '',
                'mc_price' => $mcPrice,
                'lc_price' => $lcPrice,
                'ppu'      => $ppu,
                'subtotal' => $subtotal,
            ];
        }

        [$total, $vat, $grand] = $this->computeTotals($items);

        return view('pages.condopreview', array_merge($header, [
            'rows'         => $rows,
            'total'        => $total,
            'vat'          => $vat,
            'grand'        => $grand,
            'autoDownload' => 'pdf',   
        ]));
    }

    public function exportExcel(Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $condo->load(['details' => fn($q) => $q->orderBy('no')]);

        $header = [
            'customer_name'   => $condo->customer_name,
            'address'         => $condo->address,
            'job_name'        => $condo->job_name,
            'quotation_number'=> $condo->quotation_number,
            'quotation_date'  => $condo->quotation_date,
            'payment_term'    => $condo->payment_term,
            'credits'         => $condo->credits,
        ];

        $items = $condo->details->map(function (CondoDetail $d) {
            return [
                'no'                   => $d->no,
                'details'              => $d->details,
                'amount'               => $d->amount,
                'unit'                 => $d->unit,
                'material_cost'        => $d->material_cost,
                'labor_cost'           => $d->labor_cost,
                'price_per_unit_total' => $d->price_per_unit_total,
            ];
        })->toArray();

        $rows = [];
        foreach ($items as $i => $r) {
            $amount  = (float) str_replace([',',' '], '', (string)($r['amount'] ?? 0));
            $mcPrice = (float) str_replace([',',' '], '', (string)($r['material_cost'] ?? 0));
            $lcPrice = (float) str_replace([',',' '], '', (string)($r['labor_cost'] ?? 0));
            $ppuInput = trim((string)($r['price_per_unit_total'] ?? ''));
            $ppu      = $ppuInput !== ''
                ? (float) str_replace([',',' '], '', $ppuInput)
                : ($mcPrice + $lcPrice);

            $subtotal = 0.0;
            if ($amount > 0 && $ppu > 0) {
                $subtotal = round($amount * $ppu, 2);
            } elseif ($mcPrice > 0 || $lcPrice > 0) {
                $subtotal = round($mcPrice + $lcPrice, 2);
            }

            $hasAny = false;
            foreach (['details','amount','unit','material_cost','labor_cost','price_per_unit_total'] as $k) {
                if (isset($r[$k]) && trim((string)$r[$k]) !== '') { $hasAny = true; break; }
            }
            if (! $hasAny) continue;

            $rows[] = [
                'no'       => ($r['no'] ?? ($i + 1)),
                'details'  => $r['details'] ?? '',
                'amount'   => $amount,
                'unit'     => $r['unit'] ?? '',
                'mc_price' => $mcPrice,
                'lc_price' => $lcPrice,
                'ppu'      => $ppu,
                'subtotal' => $subtotal,
            ];
        }

        [$total, $vat, $grand] = $this->computeTotals($items);

        return view('pages.condopreview', array_merge($header, [
            'rows'         => $rows,
            'total'        => $total,
            'vat'          => $vat,
            'grand'        => $grand,
            'autoDownload' => 'excel', 
        ]));
    }



}
