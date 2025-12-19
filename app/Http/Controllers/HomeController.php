<?php

namespace App\Http\Controllers;
use App\Models\HomeDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class HomeController extends Controller
{
    /** List (index) */
    public function index()
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        $homes = Home::latest('id')->paginate(15);
        return view('pages.home', compact('homes')); // render your list/table here
    }

    /** Create form */
    public function create()
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.homecreate');
    }

    /** Store */
    public function store(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $parent = $this->validated($request);

        $v = $request->validate([
            'details'                 => ['required','array','min:1'],
            'details.*.status'        => ['nullable','boolean'],
            'details.*.no'            => ['nullable','integer','min:1'],
            'details.*.category_name' => ['nullable','string','max:255'],
            'details.*.amount'        => ['nullable','numeric','min:0'],
            'details.*.unit'          => ['nullable','string','max:50'],
            'details.*.mc_price'      => ['nullable','numeric','min:0'],
            'details.*.lc_price'      => ['nullable','numeric','min:0'],
        ]);

        $totals = $request->validate([
            'final_total' => ['nullable','numeric','min:0'],
        ]);

        DB::transaction(function () use ($parent, $v, $totals) {
            $home = Home::create($parent + [
                'date'        => $parent['date'] ?? null,
                'status'      => $parent['status'] ?? true,
                'total_price' => isset($totals['final_total'])
                    ? round((float)$totals['final_total'], 2)
                    : null,
            ]);

            $rows = collect($v['details'])->map(function ($r, $i) {
                $amount = (float)($r['amount'] ?? 0);
                $mc     = (float)($r['mc_price'] ?? 0);
                $lc     = (float)($r['lc_price'] ?? 0);

                $mat    = round($amount * $mc, 2);
                $lab    = round($amount * $lc, 2);
                $grand  = round($mat + $lab, 2);

                return [
                    'status'         => (bool)($r['status'] ?? true),
                    'no'             => $r['no'] ?? ($i + 1),
                    'category_name'  => $r['category_name'] ?? null,
                    'item_name'      => $r['item_name'] ?? null,
                    'amount'         => $amount ?: null,
                    'unit'           => $this->normalizeUnitToKey($r['unit'] ?? null),
                    'mc_price'       => $mc ?: null,
                    'lc_price'       => $lc ?: null,
                    'material_total' => $amount ? $mat : null,
                    'labor_total'    => $amount ? $lab : null,
                    'grand_total'    => ($amount && ($mc || $lc)) ? $grand : null,
                ];
            })->all();

            if (!empty($rows)) {
                $home->details()->createMany($rows);
            }
        });

        return redirect()->route('home')
            ->with('success', __('Saved successfully.'));
    }

    /** Edit form */
    public function edit(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        $home->load(['details' => fn ($q) => $q->orderBy('no')]);
        foreach ($home->details as $detail) {
            $detail->unit = $this->normalizeUnitToKey($detail->unit);
        }   
        return view('pages.homeedit', compact('home'));
    }

    /** Update */
    public function update(Request $request, Home $home)
{
    App::setLocale(Session::get('locale', config('app.locale')));

    $parent = $this->validated($request);
    $parent['status'] = $parent['status'] ?? $home->status;

    $totals = $request->validate([
        'final_total' => ['nullable','numeric','min:0'],
    ]);

    // NEW: bin of ids coming from the front-end when user removed rows visually
    $deleteBag = $request->validate([
        'deleted_detail_ids'   => ['nullable','array'],
        'deleted_detail_ids.*' => ['integer','exists:home_details,id'],
    ]);

    $v = $request->validate([
        'details'                   => ['nullable','array'],
        'details.*.id'              => ['nullable','integer','exists:home_details,id'],
        'details.*._delete'         => ['nullable','boolean'],   
        'details.*.status'          => ['nullable','boolean'],
        'details.*.no'              => ['nullable','integer','min:1'],
        'details.*.category_name'   => ['nullable','string','max:255'],
        'details.*.amount'          => ['nullable','numeric','min:0'],
        'details.*.unit'            => ['nullable','string','max:50'],
        'details.*.mc_price'        => ['nullable','numeric','min:0'],
        'details.*.lc_price'        => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($home, $parent, $v, $totals, $deleteBag) {
        // 1) Update parent
        $home->update($parent + [
            'date'        => $parent['date'] ?? $home->date,
            'total_price' => isset($totals['final_total'])
                ? round((float)$totals['final_total'], 2)
                : $home->total_price,
        ]);

        // 2) Collect rows from request
        $rows = collect($v['details'] ?? []);

        // 3) Compute ids to delete (new way + old flag way) and delete them
        $toDeleteFromBin  = collect($deleteBag['deleted_detail_ids'] ?? []);
        $toDeleteFromFlag = $rows
            ->filter(fn ($r) => !empty($r['_delete']) && !empty($r['id']))
            ->pluck('id');

        $toDelete = $toDeleteFromBin
            ->merge($toDeleteFromFlag)
            ->unique()
            ->values();

        if ($toDelete->isNotEmpty()) {
            HomeDetail::where('home_id', $home->id)
                ->whereIn('id', $toDelete)
                ->delete();
        }

        // 4) Exclude deleted rows from further processing
        $rows = $rows->reject(fn ($r) => !empty($r['id']) && $toDelete->contains($r['id']));

        // 5) Update existing non-deleted rows
        $rows->filter(fn ($r) => !empty($r['id']))->each(function ($r) use ($home) {
            $detail = HomeDetail::where('home_id', $home->id)->where('id', $r['id'])->first();
            if (!$detail) return;

            $amount = (float)($r['amount'] ?? 0);
            $mc     = (float)($r['mc_price'] ?? 0);
            $lc     = (float)($r['lc_price'] ?? 0);
            $mat    = $amount * $mc;
            $lab    = $amount * $lc;
            $grand  = $mat + $lab;

            $detail->update([
                'status'         => (bool)($r['status'] ?? $detail->status),
                'no'             => $r['no'] ?? $detail->no,
                'category_name'  => $r['category_name'] ?? $detail->category_name,
                'item_name'      => $r['item_name'] ?? $detail->item_name,
                'amount'         => $r['amount'] ?? $detail->amount,
                'unit'           => $this->normalizeUnitToKey($r['unit'] ?? $detail->unit),
                'mc_price'       => $r['mc_price'] ?? $detail->mc_price,
                'lc_price'       => $r['lc_price'] ?? $detail->lc_price,
                'material_total' => $amount ? round($mat, 2) : null,
                'labor_total'    => $amount ? round($lab, 2) : null,
                'grand_total'    => ($amount && ($mc || $lc)) ? round($grand, 2) : null,
            ]);
        });

        // 6) Create new rows (no id)
        $newRows = $rows
            ->filter(fn ($r) => empty($r['id']))
            ->map(function ($r, $i) {
                $amount = (float)($r['amount'] ?? 0);
                $mc     = (float)($r['mc_price'] ?? 0);
                $lc     = (float)($r['lc_price'] ?? 0);
                $mat    = round($amount * $mc, 2);
                $lab    = round($amount * $lc, 2);
                $grand  = round($mat + $lab, 2);

                return [
                    'status'         => (bool)($r['status'] ?? true),
                    'no'             => $r['no'] ?? ($i + 1),
                    'category_name'  => $r['category_name'] ?? null,
                    'amount'         => $amount ?: null,
                    'unit'           => $this->normalizeUnitToKey($r['unit'] ?? null),
                    'mc_price'       => $mc ?: null,
                    'lc_price'       => $lc ?: null,
                    'material_total' => $amount ? $mat : null,
                    'labor_total'    => $amount ? $lab : null,
                    'grand_total'    => ($amount && ($mc || $lc)) ? $grand : null,
                ];
            })->all();

        if (!empty($newRows)) {
            $home->details()->createMany($newRows);
        }
    });

    return redirect()->route('home')->with('success', __('Updated successfully.'));
}

    /** Delete */
    public function destroy(Home $home)
    {
        DB::transaction(function () use ($home) {
            $home->details()->delete();
            $home->delete();
        });

        return redirect()->route('home')->with('success', __('Deleted successfully.'));
    }

    public function preview(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        
        // If coming from edit, we'll receive home_id
        $home = null;
        if ($request->filled('home_id')) {
            $home = Home::find($request->home_id);
        }

        // You don't really need 'date' from request anymore, so you can remove it
        $data = $request->validate([
            'project_name' => ['nullable','string','max:255'],
            'dear'         => ['nullable','string','max:255'],
            'list_name'    => ['nullable','string','max:255'],
            'house_no'     => ['nullable','string','max:255'],
            'trooper'      => ['nullable','string','max:255'],
            // 'date'      => ['nullable','date'],  // <-- can be removed

            'details'                 => ['required','array','min:1'],
            'details.*.id'            => ['nullable','integer'],
            'details.*._delete'       => ['nullable','boolean'],
            'details.*.no'            => ['nullable','integer','min:1'],
            'details.*.category_name' => ['nullable','string','max:255'],
            'details.*.item_name'     => ['nullable','string','max:255'],
            'details.*.amount'        => ['nullable','numeric','min:0'],
            'details.*.unit'          => ['nullable','string','max:50'],
            'details.*.mc_price'      => ['nullable','numeric','min:0'],
            'details.*.lc_price'      => ['nullable','numeric','min:0'],
        ]);

        // === your existing rows building logic ===
        $rows = collect($data['details'])
            ->filter(function ($r) {
                if (!empty($r['_delete'])) return false;

                $hasName = trim($r['category_name'] ?? '') !== '';
                $amt = (float)($r['amount'] ?? 0);
                $mc  = (float)($r['mc_price'] ?? 0);
                $lc  = (float)($r['lc_price'] ?? 0);
                return $hasName || ($amt>0 || $mc>0 || $lc>0);
            })
            ->values()
            ->map(function ($r, $i) {
                $amt = (float)($r['amount'] ?? 0);
                $mc  = (float)($r['mc_price'] ?? 0);
                $lc  = (float)($r['lc_price'] ?? 0);

                $mat = round($amt * $mc, 2);
                $lab = round($amt * $lc, 2);

                return [
                    'no'           => $r['no'] ?? ($i + 1),
                    'category_name'=> $r['category_name'] ?? '',
                    'item_name'    => $r['item_name'] ?? '',
                    'amount'       => $amt,
                    'unit'         => $r['unit'] ?? '',
                    'mc_price'     => $mc,
                    'lc_price'     => $lc,
                    'mat_total'    => $mat,
                    'lab_total'    => $lab,
                    'grand_total'  => round($mat + $lab, 2),
                ];
            });

        // Totals (unchanged)
        $misc  = $rows->sum('grand_total');
        $op    = round($misc * 0.15, 2);
        $ab    = round($misc + $op, 2);
        $vat   = round($ab * 0.07, 2);
        $final = round($ab + $vat, 2);

        // - If editing existing home → updated_at > created_at > today
        if ($home) {
            // EDIT PAGE → show updated_at if exists, else created_at
            $previewDate = $home->updated_at
                ?? $home->created_at
                ?? now();
        } else {
            // CREATE PAGE → always show today's date
            $previewDate = now();
        }

        $trooperStr = $data['trooper'] ?? ($home->trooper ?? '');

        if ($trooperStr === __('168 Home company')) {
            $logo_choice = '168_home';
        } elseif ($trooperStr === __('Pi Kaew company')) {
            $logo_choice = 'pi_kaew';
        } else {
            $logo_choice = 'none';
        }

        return view('pages.homepreview', [
            'project_name' => $data['project_name'] ?? '',
            'dear'         => $data['dear'] ?? '',
            'list_name'    => $data['list_name'] ?? '',
            'house_no'     => $data['house_no'] ?? '',
            'trooper'      => $data['trooper'] ?? '',
            'rows'         => $rows,
            'miscTotal'    => $misc,
            'operating'    => $op,
            'abTotal'      => $ab,
            'vat'          => $vat,
            'finalTotal'   => $final,
            'date'         => $previewDate->format('d/m/Y'),
            'logo_choice'  => $logo_choice,
        ]);
    }

        public function exportPdf(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        // Load details ordered by "no" like edit()
        $home->load(['details' => fn ($q) => $q->orderBy('no')]);

        // Build rows similar to preview(), but from DB
        $rows = $home->details->map(function (HomeDetail $d) {
            $amt = (float)($d->amount ?? 0);
            $mc  = (float)($d->mc_price ?? 0);
            $lc  = (float)($d->lc_price ?? 0);

            $mat   = $d->material_total ?? round($amt * $mc, 2);
            $lab   = $d->labor_total   ?? round($amt * $lc, 2);
            $grand = $d->grand_total   ?? round($mat + $lab, 2);

            return [
                'no'           => $d->no,
                'category_name'=> $d->category_name ?? '',
                'item_name'    => $d->item_name ?? '',
                'amount'       => $amt,
                'unit'         => $d->unit ?? '',
                'mc_price'     => $mc,
                'lc_price'     => $lc,
                'mat_total'    => $mat,
                'lab_total'    => $lab,
                'grand_total'  => $grand,
            ];
        });

        // Totals – same logic as preview()
        $misc  = $rows->sum('grand_total');
        $op    = round($misc * 0.15, 2);
        $ab    = round($misc + $op, 2);
        $vat   = round($ab * 0.07, 2);
        $final = round($ab + $vat, 2);

        // Use updated_at / created_at like preview
        $previewDate = $home->updated_at
            ?? $home->created_at
            ?? now();

        $trooperStr = $home->trooper ?? '';

        if ($trooperStr === __('168 Home company')) {
            $logo_choice = '168_home';
        } elseif ($trooperStr === __('Pi Kaew company')) {
            $logo_choice = 'pi_kaew';
        } else {
            $logo_choice = 'none';
        }

        return view('pages.homepreview', [
            'project_name' => $home->project_name,
            'dear'         => $home->dear,
            'list_name'    => $home->list_name,
            'house_no'     => $home->house_no,
            'trooper'      => $home->trooper,
            'rows'         => $rows,
            'miscTotal'    => $misc,
            'operating'    => $op,
            'abTotal'      => $ab,
            'vat'          => $vat,
            'finalTotal'   => $final,
            'date'         => $previewDate->format('d/m/Y'),
            'logo_choice'  => $logo_choice,
            'autoDownload' => 'pdf',
        ]);
    }

    public function exportExcel(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $home->load(['details' => fn ($q) => $q->orderBy('no')]);

        $rows = $home->details->map(function (HomeDetail $d) {
            $amt = (float)($d->amount ?? 0);
            $mc  = (float)($d->mc_price ?? 0);
            $lc  = (float)($d->lc_price ?? 0);

            $mat   = $d->material_total ?? round($amt * $mc, 2);
            $lab   = $d->labor_total   ?? round($amt * $lc, 2);
            $grand = $d->grand_total   ?? round($mat + $lab, 2);

            return [
                'no'           => $d->no,
                'category_name'=> $d->category_name ?? '',
                'item_name'    => $d->item_name ?? '',
                'amount'       => $amt,
                'unit'         => $d->unit ?? '',
                'mc_price'     => $mc,
                'lc_price'     => $lc,
                'mat_total'    => $mat,
                'lab_total'    => $lab,
                'grand_total'  => $grand,
            ];
        });

        $misc  = $rows->sum('grand_total');
        $op    = round($misc * 0.15, 2);
        $ab    = round($misc + $op, 2);
        $vat   = round($ab * 0.07, 2);
        $final = round($ab + $vat, 2);

        $previewDate = $home->updated_at
            ?? $home->created_at
            ?? now();

        $trooperStr = $home->trooper ?? '';

        if ($trooperStr === __('168 Home company')) {
            $logo_choice = '168_home';
        } elseif ($trooperStr === __('Pi Kaew company')) {
            $logo_choice = 'pi_kaew';
        } else {
            $logo_choice = 'none';
        }

        return view('pages.homepreview', [
            'project_name' => $home->project_name,
            'dear'         => $home->dear,
            'list_name'    => $home->list_name,
            'house_no'     => $home->house_no,
            'trooper'      => $home->trooper,
            'rows'         => $rows,
            'miscTotal'    => $misc,
            'operating'    => $op,
            'abTotal'      => $ab,
            'vat'          => $vat,
            'finalTotal'   => $final,
            'date'         => $previewDate->format('d/m/Y'),
            'logo_choice'  => $logo_choice,
            'autoDownload' => 'excel',
        ]);
    }

    /** Shared validation */
    private function validated(Request $request): array
    {
        return $request->validate([
            'project_name' => ['nullable','string','max:255'],
            'dear'         => ['nullable','string','max:255'],
            'date'         => ['nullable','date'],
            'trooper'      => ['nullable','string','max:255'],
            'house_no'     => ['nullable','string','max:255'],
            'list_name'    => ['nullable','string','max:255'],
            'status'       => ['nullable','boolean'],
        ]);
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
            'point',
            'day',
            'piece',
            'floor',
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

        // Try other locales you support (example: 'th', 'en')
        $tryLocales = ['th', 'en'];
        foreach ($unitKeys as $key) {
            foreach ($tryLocales as $loc) {
                if ($raw === \Illuminate\Support\Facades\Lang::get($key, [], $loc)) {
                    return $key;
                }
            }
        }

        // fallback: return original raw so nothing breaks
        return $raw;
    }
}
