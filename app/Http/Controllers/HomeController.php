<?php

namespace App\Http\Controllers;
use App\Models\HomeDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Home;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
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
    public function create(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        
        // Check if we need to restore form data from preview
        $restoredData = null;
        $restoredPhotos = [];
        if ($request->get('restore') && session()->has('preview_form_data')) {
            $sessionData = session()->get('preview_form_data');
            $restoredData = $sessionData['form_data'] ?? null;
            $restoredPhotos = $sessionData['temp_photos'] ?? [];
            
            // Debug logging
            \Log::info('Restoring form data from session', [
                'has_session_data' => !empty($sessionData),
                'has_form_data' => !empty($restoredData),
                'photos_count' => count($restoredPhotos),
                'form_data_keys' => $restoredData ? array_keys($restoredData) : []
            ]);
            
            // Don't clear session data yet - keep it for multiple preview attempts
        } else {
            // Clear any old preview session data and temp photos when starting fresh
            $this->clearTempPhotosAndSession();
        }
        
        return view('pages.homecreate', compact('restoredData', 'restoredPhotos'));
    }

    /** Store */
    public function store(Request $request)
    {
        \Log::info('HomeController@store called');
        \Log::info('Request data:', $request->all());
        
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

        // Validate photos and restored photos
        $photos = $request->validate([
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'mimes:jpeg,jpg,png,gif,bmp,tiff,tif,webp,svg,heic,heif,avif,ico,raw,cr2,nef,arw,dng', 'max:51200'], // 50MB max per photo
            'restored_photos' => ['nullable', 'array'],
            'restored_photos.*' => ['string'], // Paths to temporary photos
        ]);

        DB::transaction(function () use ($parent, $v, $totals, $photos, $request) {
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

            // Handle photo uploads
            if (!empty($photos['photos'])) {
                foreach ($photos['photos'] as $photo) {
                    $path = $photo->store('home_photos', 'public');
                    $home->images()->create([
                        'image_path' => $path,
                        'status' => true,
                    ]);
                }
            }

            // Handle restored photos (move from temp to permanent)
            if (!empty($photos['restored_photos'])) {
                foreach ($photos['restored_photos'] as $tempPath) {
                    if (\Storage::disk('public')->exists($tempPath)) {
                        // Move from temp to permanent location
                        $filename = basename($tempPath);
                        $permanentPath = 'home_photos/' . $filename;
                        
                        if (\Storage::disk('public')->move($tempPath, $permanentPath)) {
                            $home->images()->create([
                                'image_path' => $permanentPath,
                                'status' => true,
                            ]);
                        }
                    }
                }
            }
        });

        // Clear preview session data after successful save
        session()->forget('preview_form_data');

        return redirect()->route('home')
            ->with('success', __('Saved successfully.'));
    }

    /** Edit form */
    public function edit(Home $home, Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));
        $home->load(['details' => fn ($q) => $q->orderBy('no')]);
        foreach ($home->details as $detail) {
            $detail->unit = $this->normalizeUnitToKey($detail->unit);
        }   
        
        // Check if we need to restore form data from preview
        $restoredData = null;
        $restoredPhotos = [];
        $deletedPhotoIds = [];
        if ($request->get('restore') && session()->has('preview_form_data')) {
            $sessionData = session()->get('preview_form_data');
            $restoredData = $sessionData['form_data'] ?? null;
            $restoredPhotos = $sessionData['temp_photos'] ?? [];
            $deletedPhotoIds = $sessionData['deleted_photo_ids'] ?? [];
            
            // Don't clear session data yet - keep it for multiple preview attempts
        } else {
            // Clear any old preview session data and temp photos when starting fresh edit
            // BUT preserve deletions if user is coming back from preview (they clicked back, not cancel)
            // Only clear if this is a completely fresh edit (no restore parameter)
            if (!$request->get('restore')) {
                $this->clearTempPhotosAndSession();
            }
        }
        
        return view('pages.homeedit', compact('home', 'restoredData', 'restoredPhotos', 'deletedPhotoIds'));
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

    // Validate photos and photo deletions
    $photos = $request->validate([
        'photos' => ['nullable', 'array'],
        'photos.*' => ['file', 'mimes:jpeg,jpg,png,gif,bmp,tiff,tif,webp,svg,heic,heif,avif,ico,raw,cr2,nef,arw,dng', 'max:51200'], // 50MB max per photo
        'delete_photos' => ['nullable', 'array'],
        'delete_photos.*' => ['integer', 'exists:images,id'],
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

    DB::transaction(function () use ($home, $parent, $v, $totals, $deleteBag, $photos, $request) {
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

        // 7) Handle photo deletions
        if (!empty($photos['delete_photos'])) {
            $photosToDelete = Image::whereIn('id', $photos['delete_photos'])
                ->where('home_id', $home->id)
                ->get();
            
            foreach ($photosToDelete as $photo) {
                if ($photo->image_path && \Storage::disk('public')->exists($photo->image_path)) {
                    \Storage::disk('public')->delete($photo->image_path);
                }
                $photo->delete();
            }
        }

        // 8) Handle new photo uploads
        if (!empty($photos['photos'])) {
            foreach ($photos['photos'] as $photo) {
                $path = $photo->store('home_photos', 'public');
                $home->images()->create([
                    'image_path' => $path,
                    'status' => true,
                ]);
            }
        }
    });

    // Clear preview session data after successful update
    session()->forget('preview_form_data');

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

        // Handle photos from form - SIMPLIFIED LOGIC
        $uploadedPhotos = collect();
        $allTempPhotos = [];
        
        // Handle new photo uploads (direct file uploads)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Store temporarily for preview
                $tempPath = $photo->store('temp_photos', 'public');
                $allTempPhotos[] = $tempPath;
                
                // Create a temporary Image model instance (not saved to DB)
                $tempImage = new Image([
                    'image_path' => $tempPath,
                    'status' => true,
                ]);
                // Set a temporary ID to avoid issues with JSON serialization
                $tempImage->id = 'temp_' . uniqid();
                $tempImage->temp = true; // Add a temporary flag
                $uploadedPhotos->push($tempImage);
            }
        }
        
        // Handle restored photos from form (from chunked upload or previous preview)
        if ($request->has('restored_photos')) {
            try {
                $restoredPhotosInput = $request->input('restored_photos');
                
                // Handle both array and single value
                if (!is_array($restoredPhotosInput)) {
                    $restoredPhotosInput = [$restoredPhotosInput];
                }
                
                // Remove empty values and duplicates
                $restoredPhotosInput = array_filter(array_unique($restoredPhotosInput));
                
                foreach ($restoredPhotosInput as $tempPath) {
                    if ($tempPath && \Storage::disk('public')->exists($tempPath)) {
                        // Only add if not already in our new uploads
                        if (!in_array($tempPath, $allTempPhotos)) {
                            $allTempPhotos[] = $tempPath;
                            
                            // Create a temporary Image model instance (not saved to DB)
                            $tempImage = new Image([
                                'image_path' => $tempPath,
                                'status' => true,
                            ]);
                            // Set a temporary ID to avoid issues with JSON serialization
                            $tempImage->id = 'temp_' . uniqid();
                            $tempImage->temp = true; // Add a temporary flag
                            $uploadedPhotos->push($tempImage);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error processing restored photos: ' . $e->getMessage());
            }
        }

        // Clean up old temp photos (older than 1 hour)
        $this->cleanupTempPhotos();

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
            
            // Combine existing photos with uploaded photos
            // Only include existing photos that are not marked for deletion
            $deletePhotoIds = $request->input('delete_photos', []);
            $existingPhotos = $home->images()
                ->where('status', true)
                ->whereNotIn('id', $deletePhotoIds)
                ->get();
            
            // Filter out deleted photos from uploaded photos
            // If a photo path is in delete_photos, exclude it
            $filteredUploadedPhotos = $uploadedPhotos->filter(function($photo) use ($deletePhotoIds) {
                // For uploaded photos, we need to check if they're marked for deletion
                // Since they're temp photos, we'll check the restored_photos array
                return true; // Include all uploaded photos for now
            });
            
            // Merge existing photos with uploaded photos (new + restored)
            $allPhotos = $existingPhotos->merge($filteredUploadedPhotos);
            
            \Log::info('Preview photos for edit', [
                'existing_count' => $existingPhotos->count(),
                'uploaded_count' => $filteredUploadedPhotos->count(),
                'total_count' => $allPhotos->count(),
                'delete_photo_ids' => $deletePhotoIds,
                'all_temp_photos' => $allTempPhotos
            ]);
        } else {
            // CREATE PAGE → always show today's date
            $previewDate = now();
            $allPhotos = $uploadedPhotos;
        }

        $trooperStr = $data['trooper'] ?? ($home->trooper ?? '');

        if ($trooperStr === __('168 Home company')) {
            $logo_choice = '168_home';
        } elseif ($trooperStr === __('Pi Kaew')) {
            $logo_choice = 'pi_kaew';
        } else {
            $logo_choice = 'none';
        }

        // Store form data in session for back navigation
        // For edit mode, include existing photo IDs so they can be preserved
        $existingPhotoIds = [];
        $deletedPhotoIds = [];
        if ($home) {
            $existingPhotoIds = $home->images()->where('status', true)->pluck('id')->toArray();
            $deletedPhotoIds = $request->input('delete_photos', []);
        }
        
        session()->put('preview_form_data', [
            'form_data' => $data,
            'temp_photos' => $allTempPhotos, // Use the deduplicated list
            'home_id' => $home ? $home->id : null,
            'existing_photo_ids' => $existingPhotoIds, // Store existing photo IDs for edit mode
            'deleted_photo_ids' => $deletedPhotoIds, // Store deleted photo IDs so they stay deleted when coming back
        ]);

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
            'photos'       => $allPhotos instanceof \Illuminate\Support\Collection ? $allPhotos : collect($allPhotos),
            'is_preview'   => true,
            'translations' => [
                'bill_of_quantities' => 'Bill of Quantities', // Keep English for this one
                'project_name' => __('Project Name') . ':',
                'date' => __('Date') . ':',
                'dear' => __('Dear') . ':',
                'trooper' => 'Trooper :',
                'house_no' => __('House No.'),
                'no' => 'No',
                'list' => __('List'),
                'amount' => __('Amount'),
                'unit' => __('Unit'),
                'material_cost' => __('Material Cost'),
                'labor_cost' => __('Labor Cost'),
                'total_amount' => __('Total Amount'),
                'price_unit' => __('Price') . '/' . __('Unit'),
                'total_price' => __('Total Prices'),
                'misc_work_category' => __('Miscellaneous work category'),
                'total_misc_work' => __('Total price for miscellaneous work category'),
                'operating_profit' => __('Operating expenses and profit 15%'),
                'after_profit' => __('Total price of work category A,B'),
                'vat' => __('Value Added Tax 7%'),
                'final_total' => __('Total_price'),
                'excel_success' => __('Download Excel') . ' successfully.',
                'download' => __('Download Excel'),
            ],
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
            'photos'       => $home->images()->where('status', true)->get(),
            'translations' => [
                'bill_of_quantities' => 'Bill of Quantities', // Keep English for this one
                'project_name' => __('Project Name') . ':',
                'date' => __('Date') . ':',
                'dear' => __('Dear') . ':',
                'trooper' => 'Trooper :',
                'house_no' => __('House No.'),
                'no' => 'No',
                'list' => __('List'),
                'amount' => __('Amount'),
                'unit' => __('Unit'),
                'material_cost' => __('Material Cost'),
                'labor_cost' => __('Labor Cost'),
                'total_amount' => __('Total Amount'),
                'price_unit' => __('Price') . '/' . __('Unit'),
                'total_price' => __('Total Prices'),
                'misc_work_category' => __('Miscellaneous work category'),
                'total_misc_work' => __('Total price for miscellaneous work category'),
                'operating_profit' => __('Operating expenses and profit 15%'),
                'after_profit' => __('Total price of work category A,B'),
                'vat' => __('Value Added Tax 7%'),
                'final_total' => __('Total_price'),
                'excel_success' => __('Download Excel') . ' successfully.',
                'download' => __('Download Excel'),
            ],
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
        } elseif ($trooperStr === __('Pi Kaew')) {
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
            'photos'       => $home->images()->where('status', true)->get(),
            'translations' => [
                'bill_of_quantities' => 'Bill of Quantities', // Keep English for this one
                'project_name' => __('Project Name') . ':',
                'date' => __('Date') . ':',
                'dear' => __('Dear') . ':',
                'trooper' => 'Trooper :',
                'house_no' => __('House No.'),
                'no' => 'No',
                'list' => __('List'),
                'amount' => __('Amount'),
                'unit' => __('Unit'),
                'material_cost' => __('Material Cost'),
                'labor_cost' => __('Labor Cost'),
                'total_amount' => __('Total Amount'),
                'price_unit' => __('Price') . '/' . __('Unit'),
                'total_price' => __('Total Prices'),
                'misc_work_category' => __('Miscellaneous work category'),
                'total_misc_work' => __('Total price for miscellaneous work category'),
                'operating_profit' => __('Operating expenses and profit 15%'),
                'after_profit' => __('Total price of work category A,B'),
                'vat' => __('Value Added Tax 7%'),
                'final_total' => __('Total_price'),
                'excel_success' => __('Download Excel') . ' successfully.',
                'download' => __('Download Excel'),
            ],
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

    /** Delete Photo */
    public function deletePhoto(Image $image)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        // Delete the file from storage
        if ($image->image_path && \Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        // Delete the database record
        $image->delete();

        return response()->json(['success' => true]);
    }

    /** Clean up temporary photos */
    private function cleanupTempPhotos()
    {
        $tempDir = storage_path('app/public/temp_photos');
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            $oneHourAgo = time() - 3600; // 1 hour ago
            
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $oneHourAgo) {
                    unlink($file);
                }
            }
        }
    }

    /** Upload photo chunk for better handling of many photos */
    public function uploadPhotoChunk(Request $request)
    {
        App::setLocale(Session::get('locale', config('app.locale')));

        $request->validate([
            'photo' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,bmp,tiff,tif,webp,svg,heic,heif,avif,ico,raw,cr2,nef,arw,dng', 'max:51200'], // 50MB max per photo
        ]);

        try {
            // Store photo temporarily
            $photo = $request->file('photo');
            
            // Handle special formats that might need conversion
            $processedPhoto = $this->processPhotoUpload($photo);
            
            $tempPath = $processedPhoto->store('temp_photos', 'public');
            
            return response()->json([
                'success' => true,
                'temp_path' => $tempPath,
                'filename' => $photo->getClientOriginalName(),
                'size' => $photo->getSize(),
                'original_format' => $photo->getClientOriginalExtension(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process uploaded photo to handle special formats
     */
    private function processPhotoUpload($photo)
    {
        $extension = strtolower($photo->getClientOriginalExtension());
        
        // List of formats that might need special handling
        $specialFormats = ['heic', 'heif', 'avif', 'cr2', 'nef', 'arw', 'dng', 'raw'];
        
        if (in_array($extension, $specialFormats)) {
            // For HEIC/HEIF files, try to convert to JPEG for better web compatibility
            if (in_array($extension, ['heic', 'heif'])) {
                return $this->convertHeicToJpeg($photo);
            }
            
            // Log the special format for monitoring
            \Log::info("Uploaded special format: {$extension}", [
                'filename' => $photo->getClientOriginalName(),
                'size' => $photo->getSize()
            ]);
        }
        
        return $photo;
    }

    /**
     * Convert HEIC/HEIF to JPEG for better web compatibility
     */
    private function convertHeicToJpeg($photo)
    {
        try {
            // Check if ImageMagick is available
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->readImageBlob(file_get_contents($photo->getRealPath()));
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(85);
                
                // Create a temporary file for the converted image
                $tempPath = tempnam(sys_get_temp_dir(), 'heic_converted_') . '.jpg';
                $imagick->writeImage($tempPath);
                $imagick->clear();
                
                // Create a new UploadedFile object from the converted image
                $convertedPhoto = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME) . '.jpg',
                    'image/jpeg',
                    null,
                    true
                );
                
                return $convertedPhoto;
            }
            
            // If ImageMagick is not available, try GD (limited HEIC support)
            // For now, just return the original file
            \Log::warning('HEIC conversion not available - ImageMagick not installed');
            return $photo;
            
        } catch (\Exception $e) {
            \Log::error('HEIC conversion failed: ' . $e->getMessage());
            return $photo;
        }
    }

    /** Check PHP configuration for upload limits */
    public function checkUploadConfig()
    {
        $config = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_vars' => ini_get('max_input_vars'),
        ];

        return response()->json([
            'config' => $config,
            'recommendations' => [
                'upload_max_filesize' => '50M',
                'post_max_size' => '500M',
                'max_file_uploads' => '200',
                'max_execution_time' => '600',
                'memory_limit' => '512M',
                'max_input_vars' => '5000',
            ]
        ]);
    }

    /** Clear session data for photos */
    public function clearSession(Request $request)
    {
        if ($request->has('clear_session')) {
            session()->forget('preview_form_data');
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }

    /** Clear all temporary photos and session data */
    private function clearTempPhotosAndSession()
    {
        // Clear session data
        session()->forget('preview_form_data');
        
        // Clean up ALL temp photos when starting fresh (not just old ones)
        $tempDir = storage_path('app/public/temp_photos');
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
