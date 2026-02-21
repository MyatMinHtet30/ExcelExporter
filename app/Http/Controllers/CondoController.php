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
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

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
    public function create(Request $request)
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

        // Check if we're restoring from preview
        $restoredData = null;
        $restoredPhotos = [];
        
        if ($request->has('restore') && session()->has('condo_preview_form_data')) {
            $sessionData = session()->get('condo_preview_form_data');
            $restoredData = $sessionData['form_data'] ?? null;
            $restoredPhotos = $sessionData['temp_photos'] ?? [];
            
            \Log::info('Restoring condo form data from session', [
                'has_data' => !is_null($restoredData),
                'photo_count' => count($restoredPhotos)
            ]);
        }

        return view('pages.createcondo', compact('nextQuotationNumber', 'restoredData', 'restoredPhotos')); 
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

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Preserve original filename
                $originalName = $photo->getClientOriginalName();
                $path = $photo->storeAs('condo_photos', $originalName, 'public');
                
                $condo->images()->create([
                    'image_path' => $path,
                    'status' => true,
                ]);
            }
        }
        
        // Handle restored photos from preview
        if ($request->has('restored_photos') && is_array($request->input('restored_photos'))) {
            foreach ($request->input('restored_photos') as $tempPath) {
                if ($tempPath && \Storage::disk('public')->exists($tempPath)) {
                    // Move from temp to permanent location
                    $filename = basename($tempPath);
                    $newPath = 'condo_photos/' . $filename;
                    
                    \Storage::disk('public')->move($tempPath, $newPath);
                    
                    $condo->images()->create([
                        'image_path' => $newPath,
                        'status' => true,
                    ]);
                }
            }
        }
        
        // Clean up temp photos from session
        $this->cleanupTempPhotos();

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

        $condo->load(['details' => fn($q) => $q->orderBy('no'), 'images']);

        foreach ($condo->details as $detail) {
            $detail->unit = $this->normalizeUnitToKey($detail->unit);
        }


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

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('condo_photos', 'public');
                $condo->images()->create([
                    'image_path' => $path,
                    'status' => true,
                ]);
            }
        }

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
        // DEBUG: Check if photos are in the request
        \Log::info('Condo Preview - Request debug', [
            'has_file_photos' => $request->hasFile('photos'),
            'has_photos_input' => $request->has('photos'),
            'all_files' => $request->allFiles(),
            'photos_value' => $request->input('photos'),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
        ]);
        
        // Also log ALL request data to see what's coming through
        \Log::info('Condo Preview - Full request', [
            'all_input' => $request->except(['_token']),
            'files_count' => count($request->allFiles()),
        ]);
        
        // Handle language switching for preview
        if ($request->has('preview_locale')) {
            $previewLocale = $request->input('preview_locale');
            if (in_array($previewLocale, ['en', 'th'])) {
                Session::put('locale', $previewLocale);
                \Log::info('Preview locale changed to: ' . $previewLocale);
            }
        }
        
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

        // Get photos - handle both existing and newly uploaded
        $photos = collect();
        
        // If editing existing condo, get existing photos
        if ($condoId) {
            $model = Condo::with('images')->find($condoId);
            if ($model) {
                $existingPhotos = $model->images()->where('status', true)->get();
                $photos = $photos->merge($existingPhotos);
                \Log::info('Condo Preview - Existing photos loaded', [
                    'condo_id' => $condoId,
                    'photo_count' => $existingPhotos->count()
                ]);
            }
        }
        
        // Handle new photo uploads (temporary for preview)
        $allTempPhotos = [];
        if ($request->hasFile('photos')) {
            \Log::info('Condo Preview - New photos uploaded', [
                'count' => count($request->file('photos'))
            ]);
            
            foreach ($request->file('photos') as $photo) {
                // Store temporarily for preview
                $tempPath = $photo->store('temp_photos', 'public');
                $allTempPhotos[] = $tempPath;
                
                // Create a temporary Image model instance (not saved to DB)
                $tempImage = new \App\Models\Image([
                    'image_path' => $tempPath,
                    'status' => true,
                ]);
                $tempImage->id = 'temp_' . uniqid();
                $tempImage->temp = true;
                $photos->push($tempImage);
            }
        }
        
        // Handle restored photos from previous preview
        if ($request->has('restored_photos') && is_array($request->input('restored_photos'))) {
            \Log::info('Condo Preview - Restored photos', [
                'count' => count($request->input('restored_photos'))
            ]);
            
            foreach ($request->input('restored_photos') as $tempPath) {
                if ($tempPath && \Storage::disk('public')->exists($tempPath)) {
                    if (!in_array($tempPath, $allTempPhotos)) {
                        $allTempPhotos[] = $tempPath;
                        
                        $tempImage = new \App\Models\Image([
                            'image_path' => $tempPath,
                            'status' => true,
                        ]);
                        $tempImage->id = 'temp_' . uniqid();
                        $tempImage->temp = true;
                        $photos->push($tempImage);
                    }
                }
            }
        }

        \Log::info('Condo Preview - Total photos', [
            'total_count' => $photos->count(),
            'photos' => $photos->pluck('image_path')->toArray()
        ]);

        // Store form data in session for back navigation
        session()->put('condo_preview_form_data', [
            'form_data' => $request->except(['photos', '_token']),
            'temp_photos' => $allTempPhotos,
            'condo_id' => $condoId,
        ]);

        return view('pages.condopreview', array_merge($header, [
            'rows'  => $rows,
            'total' => $total,
            'vat'   => $vat,
            'grand' => $grand,
            'photos' => $photos instanceof \Illuminate\Support\Collection ? $photos : collect($photos),
        ]));
    }

    public function exportPdf(Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $condo->load([
            'details' => fn($q) => $q->orderBy('no'),
            'images' => fn($q) => $q->where('status', true),
        ]);

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
            'photos'       => $condo->images,
            'autoDownload' => 'pdf',   
        ]));
    }

    public function exportExcel(Condo $condo)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $condo->load([
            'details' => fn($q) => $q->orderBy('no'),
            'images' => fn($q) => $q->where('status', true),
        ]);

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
            'photos'       => $condo->images,
            'autoDownload' => 'excel',
            'translations' => [
                'quotation' => __('Quotation'),
                'company_address' => __('Address of PITA BUILD Company Limited (Head Office)'),
                'phone' => __('Phone'),
                'email' => __('Email'),
                'taxpayer_number' => __('Taxpayer Identification Number'),
                'customer_name' => __('Customer Name'),
                'address' => __('Address'),
                'job_name' => __('Job Name'),
                'quotation_number' => __('Quotation Number'),
                'quotation_date' => __('Date'),
                'payment_term' => __('Payment Term'),
                'credits' => __('Credits'),
                'no' => __('No'),
                'details' => __('Details'),
                'amount' => __('Amount'),
                'units' => __('Units'),
                'material_cost' => __('Material Cost'),
                'labor_cost' => __('Labor Cost'),
                'price_amount' => __('Price Amount'),
                'subtotal' => __('Subtotal'),
                'total' => __('Total'),
                'tax' => __('Tax (7%)'),
                'total_price' => __('Total Price'),
                'photos' => __('Project Photos'),
                'excel_success' => __('Excel downloaded successfully.'),
                'download' => __('Download'),
            ],
        ]));
    }

    private function normalizeUnitToKey(?string $raw): ?string
    {
        if ($raw === null || $raw === '') return $raw;

        $unitKeys = [
            'sq.m',
            'm',
            'lump sum',
            'leaf',
            'trip',
            'set',
            'sheet',
            'unit',
        ];

        // if already a key, return it
        if (in_array($raw, $unitKeys, true)) {
            return $raw;
        }

        // Check current locale translation first
        foreach ($unitKeys as $key) {
            if ($raw === __($key)) {
                return $key;
            }
        }

        // Try other locales you support (e.g. 'th', 'en')
        $tryLocales = ['th', 'en'];
        foreach ($unitKeys as $key) {
            foreach ($tryLocales as $loc) {
                if ($raw === Lang::get($key, [], $loc)) {
                    return $key;
                }
            }
        }

        // fallback: return original raw (so nothing breaks)
        return $raw;
    }
    
    /**
     * Clean up temporary photos from session
     */
    private function cleanupTempPhotos()
    {
        if (session()->has('condo_preview_form_data')) {
            $sessionData = session()->get('condo_preview_form_data');
            $tempPhotos = $sessionData['temp_photos'] ?? [];
            
            foreach ($tempPhotos as $tempPath) {
                if ($tempPath && \Storage::disk('public')->exists($tempPath)) {
                    \Storage::disk('public')->delete($tempPath);
                    \Log::info('Deleted temp photo', ['path' => $tempPath]);
                }
            }
            
            // Clear the session data
            session()->forget('condo_preview_form_data');
        }
    }
    
    /**
     * Clean up temp photos when user navigates away
     */
    public function cleanupSession(Request $request)
    {
        $this->cleanupTempPhotos();
        return response()->json(['success' => true]);
    }



}
