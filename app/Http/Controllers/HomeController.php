<?php

namespace App\Http\Controllers;
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

    // 1) Validate parent
    $parent = $this->validated($request);

    // 2) Validate children (details[])
    $v = $request->validate([
        'details'                 => ['required','array','min:1'],
        'details.*.status'        => ['nullable','boolean'],
        'details.*.no'            => ['nullable','integer','min:1'],
        'details.*.category_name' => ['nullable','string','max:255'],
        'details.*.item_name'     => ['nullable','string','max:255'],
        'details.*.amount'        => ['nullable','numeric','min:0'],
        'details.*.unit'          => ['nullable','string','max:50'],
        'details.*.mc_price'      => ['nullable','numeric','min:0'],
        'details.*.lc_price'      => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($parent, $v) {
    $home = Home::create($parent + ['status' => $parent['status'] ?? true]);

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

        $grandSum = collect($rows)->sum(function ($r) {
            return (float)($r['grand_total'] ?? 0);
        });
        $home->update(['total_price' => round($grandSum, 2)]);

        if (!empty($rows)) {
            $home->details()->createMany($rows);
        }
    });


    // 5) Back to list
    return redirect()->route('home')->with('success', __('Saved successfully.'));
}

    /** Show (optional) */
    public function show(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.home_show', compact('home'));
    }

    /** Edit form */
    public function edit(Home $home)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.homeedit', compact('home'));
    }

    /** Update */
    public function update(Request $request, Home $home)
    {
        $data = $this->validated($request);
        $home->update($data + ['status' => $data['status'] ?? $home->status]);

        return redirect()->route('home')->with('success', __('Updated successfully.'));
    }

    /** Delete */
    public function destroy(Home $home)
    {
        $home->delete();
        return redirect()->route('home')->with('success', __('Deleted successfully.'));
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
