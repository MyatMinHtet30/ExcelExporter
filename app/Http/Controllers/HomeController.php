<?php

namespace App\Http\Controllers;
use App\Models\HomeDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
                    'unit'           => $r['unit'] ?? null,
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

        return redirect()->route('home')->with('success', __('Saved successfully.'));
    }

    /** Edit form */
    public function edit(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        $home->load(['details' => fn ($q) => $q->orderBy('no')]);   // important
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

        DB::transaction(function () use ($home, $parent, $v, $totals) {
            // Update parent
            $home->update($parent + [
            'date'        => $parent['date'] ?? $home->date,
            'total_price' => isset($totals['final_total'])
                ? round((float)$totals['final_total'], 2)
                : $home->total_price,
        ]);

            $rows = collect($v['details'] ?? []);

            $toDelete = $rows->filter(fn ($r) => !empty($r['_delete']) && !empty($r['id']))->pluck('id');
            if ($toDelete->isNotEmpty()) {
                HomeDetail::where('home_id', $home->id)->whereIn('id', $toDelete)->delete();
            }

            $rows->filter(fn ($r) => empty($r['_delete']) && !empty($r['id']))->each(function ($r) use ($home) {
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
                    'unit'           => $r['unit'] ?? $detail->unit,
                    'mc_price'       => $r['mc_price'] ?? $detail->mc_price,
                    'lc_price'       => $r['lc_price'] ?? $detail->lc_price,
                    'material_total' => $amount ? round($mat, 2) : null,
                    'labor_total'    => $amount ? round($lab, 2) : null,
                    'grand_total'    => ($amount && ($mc || $lc)) ? round($grand, 2) : null,
                ]);
            });

            $newRows = $rows->filter(fn ($r) => empty($r['_delete']) && empty($r['id']))->map(function ($r, $i) {
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
                    'unit'           => $r['unit'] ?? null,
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
        // Validate incoming fields from both Create and Edit forms
        $data = $request->validate([
            'project_name' => ['nullable','string','max:255'],
            'dear'         => ['nullable','string','max:255'],
            'list_name'    => ['nullable','string','max:255'],
            'house_no'     => ['nullable','string','max:255'],
            'trooper'      => ['nullable','string','max:255'],
            'date'         => ['nullable','date'],

            // details
            'details'                 => ['required','array','min:1'],
            'details.*.id'            => ['nullable','integer'],
            'details.*._delete'       => ['nullable','boolean'],   // ← from Edit
            'details.*.no'            => ['nullable','integer','min:1'],
            'details.*.category_name' => ['nullable','string','max:255'],
            'details.*.item_name'     => ['nullable','string','max:255'],
            'details.*.amount'        => ['nullable','numeric','min:0'],
            'details.*.unit'          => ['nullable','string','max:50'],
            'details.*.mc_price'      => ['nullable','numeric','min:0'],
            'details.*.lc_price'      => ['nullable','numeric','min:0'],
        ]);

        // Build rows: skip deleted and empty
        $rows = collect($data['details'])
            ->filter(function ($r) {
                // remove rows toggled for deletion in Edit
                if (!empty($r['_delete'])) return false;

                // consider empty if no name and all numbers are zero/empty
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

        // Totals
        $misc = $rows->sum('grand_total');
        $op   = round($misc * 0.15, 2);
        $ab   = round($misc + $op, 2);
        $vat  = round($ab * 0.07, 2);
        $final= round($ab + $vat, 2);

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
            'date'         => !empty($data['date'])
                ? \Carbon\Carbon::parse($data['date'])->format('m-d-y')
                : '',
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
}
