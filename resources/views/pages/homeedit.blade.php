@extends('layouts.master')

@section('title', __('Edit Home Data Entry'))

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/home-forminput-table.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/home-common.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/photo-upload.css') }}" rel="stylesheet">
    <style>
        .row-deleted {
            opacity: .45;
            filter: grayscale(0.6);
        }
    </style>
@endpush

@section('content')
    <form id="home-form" action="{{ route('home.update', $home) }}" method="POST" data-preview-action="{{ route('home.preview') }}" novalidate enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="home_id" value="{{ $home->id }}">

        <div id="deleted-bin"></div>

        <div class="xp-contentbar">
            <div class="row">

                {{-- Top info cards (same as create) --}}
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Project Name') }} <span class="star">*</span></h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="project_name" id="project_name"
                                            value="{{ old('project_name', $home->project_name) }}" required
                                            data-error-required="{{ __('Project name is required') }}">
                                        @error('project_name')<small
                                        class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- List Name --}}
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('List Name') }} <span class="star">*</span></h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="list_name" id="list_name"
                                            value="{{ old('list_name', $home->list_name) }}" required
                                            data-error-required="{{ __('List name is required') }}">
                                        @error('list_name')<small
                                        class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dear --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Dear') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="dear" id="dear"
                                            value="{{ old('dear', $home->dear) }}" nullable>
                                        @error('dear')<small
                                        class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- House No + Trooper --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('House No') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="house_no" id="house_no"
                                            value="{{ old('house_no', $home->house_no) }}">
                                        @error('house_no')<small
                                        class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Trooper') }} <span class="star">*</span></h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <select name="trooper" id="trooper" class="form-control" required
                                            data-error-required="{{ __('Trooper is required') }}">

                                            <option value="{{ __('168 Home company') }}"
                                                {{ old('trooper', $home->trooper) === __('168 Home company') ? 'selected' : '' }}>
                                                {{ __('168 Home company') }}
                                            </option>

                                            <option value="{{ __('Pi Kaew') }}"
                                                {{ old('trooper', $home->trooper) === __('Pi Kaew') ? 'selected' : '' }}>
                                                {{ __('Pi Kaew') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Existing Details ===== --}}
                <div id="rows-container" class="col-12">
                    @php $i = 0; @endphp
                    @foreach($home->details as $detail)
                        <div class="card m-b-2 item-row" data-row-index="{{ $i }}">
    <div class="card-header bg-white">

        {{-- Line 1 --}}
        <div class="row g-3 g-compact align-items-start">
            <input type="hidden" name="details[{{ $i }}][id]" value="{{ $detail->id }}">
            <input type="hidden" name="details[{{ $i }}][_delete]" value="0" class="js-delete-flag">

            <!-- No -->
            <div class="col-3 col-sm-2 col-md-1 field-col">
                <label class="form-label">{{ __('No') }}</label>
                <div class="input-group input-42">
                    <input type="number" class="form-control readonly-input serial"
                           name="details[{{ $i }}][no]"
                           value="{{ $detail->no }}" min="1" readonly>
                </div>
            </div>

            <!-- Category -->
            <div class="col-12 col-sm-6 col-md-3 field-col">
                <label class="form-label">{{ __('Category') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <input type="text" class="form-control"
                           name="details[{{ $i }}][category_name]"
                           value="{{ $detail->category_name }}" required
                           data-error-required="{{ __('Category is required') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn"
                            onclick="startDictation(this)">
                        <i class="fas fa-microphone"></i>
                    </button>
                </div>
            </div>

            <!-- Amount -->
            <div class="col-6 col-md-2 field-col">
                <label class="form-label">{{ __('Amount') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <input type="text" step="0.01" class="form-control js-amount"
                           name="details[{{ $i }}][amount]"
                           value="{{ $detail->amount }}" required
                           data-error-required="{{ __('Amount is required') }}">
                </div>
            </div>

            <!-- Unit -->
            @php $unitKeys = ['sq.m','m','lump sum','leaf','trip','point','day','piece','floor','set','sheet','unit']; @endphp

            <div class="col-6 col-md-2 field-col">
                <label class="form-label">{{ __('Unit') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <select class="form-control units"
                            name="details[{{ $i }}][unit]"
                            required
                            data-error-required="{{ __('Unit is required') }}">
                    <option value="" selected disabled>{{ __('Select Unit') }}</option>
                    @foreach ($unitKeys as $key)
                        <option value="{{ $key }}" {{ old("details.$i.unit", $detail->unit) === $key ? 'selected' : '' }}>
                        {{ __($key) }}
                        </option>
                    @endforeach
                    </select>
                </div>
            </div>

            <!-- Material Price / Unit -->
            <div class="col-6 col-md-2 field-col">
                <label class="form-label">{{ __('Material Price') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <input type="text" step="0.01" class="form-control js-mc"
                           name="details[{{ $i }}][mc_price]"
                           value="{{ $detail->mc_price }}"
                           data-error-number="{{ __('Please enter number only') }}">
                </div>
            </div>

            <!-- Labor Price / Unit -->
            <div class="col-6 col-md-2 field-col">
                <label class="form-label">{{ __('Labor Price') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <input type="text" step="0.01" class="form-control js-lc"
                           name="details[{{ $i }}][lc_price]"
                           value="{{ $detail->lc_price }}"
                           data-error-number="{{ __('Please enter number only') }}">
                </div>
            </div>
        </div>

        {{-- Line 2 (totals, same width as create, aligned right) --}}
        <div class="row g-3 g-compact align-items-end pt-2 fields-line-2 justify-content-end">

            <!-- Material Total -->
            <div class="col-12 col-md-2 field-col">
                <label class="form-label">{{ __('Material Total') }}</label>
                <div class="input-group input-42">
                    <input type="text"
                           class="form-control readonly-input js-mat-total"
                           value="{{ number_format((float) $detail->material_total, 2) }}"
                           readonly>
                </div>
            </div>

            <!-- Labor Total -->
            <div class="col-12 col-md-2 field-col">
                <label class="form-label">{{ __('Labor Total') }}</label>
                <div class="input-group input-42">
                    <input type="text"
                           class="form-control readonly-input js-lab-total"
                           value="{{ number_format((float) $detail->labor_total, 2) }}"
                           readonly>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="col-12 col-md-2 field-col">
                <label class="form-label">{{ __('Grand Total') }}</label>
                <div class="input-group input-42">
                    <input type="text"
                           class="form-control readonly-input js-grand-total"
                           value="{{ number_format((float) $detail->grand_total, 2) }}"
                           readonly>
                </div>
            </div>

        </div>

        <div class="row pt-2">
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-danger remove-row"
                        onclick="markRowDeleted(this)">&times;</button>
            </div>
        </div>

    </div>
</div>
                        @php $i++; @endphp
                    @endforeach
                </div>

                <!-- ===== Desktop Actions + Summary (Keep as is - hidden on mobile) ===== -->
                <div class="col-lg-12 d-none d-md-block">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <div class="row">
                                <!-- Left: buttons -->
                                <div class="col-md-8 col-5">
                                    <div class="card-body left-controls">
                                        <div class="form-group d-none d-md-block">
                                            <button type="button" class="btn btn-primary" id="add-row-btn">+
                                                {{ __('Add Row') }}</button>
                                        </div>
                                        <div class="form-group btn-generate d-flex flex-wrap gap-2">
                                            <a href="{{ route('home') }}" class="btn btn-secondary" onclick="clearEditSessionOnCancel()">
                                                {{ __('Cancel') }}
                                            </a>
                                            <button type="button" class="btn btn-primary" id="preview-btn">
                                                {{ __('Preview') }}
                                            </button>
                                            <button type="submit" class="btn btn-success" id="generate-btn">
                                                {{ __('Update') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: compact totals -->
                                <div class="col-4 col-sm-4 col-md-3 col-lg-4 ms-md-auto summary-col">
                                    <div class="text-end" style="min-width:200px">
                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('Total') }}:</strong>
                                            <span class="ms-2" id="miscDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-none d-md-inline">
                                                    {{ __('Operating + Profit (15%)') }}
                                                </span>
                                                <span class="d-inline d-md-none">
                                                    <span class="d-block">{{ __('Operating +') }}</span>
                                                    <span class="d-block">{{ __('Profit (15%)') }}</span>
                                                </span>
                                            </strong>
                                            <span class="ms-2" id="operatingDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-none d-md-inline">
                                                    {{ __('Category A,B Total') }}
                                                </span>
                                                <span class="d-inline d-md-none">
                                                    <span class="d-block">{{ __('Category A,B') }}</span>
                                                    <span class="d-block">{{ __('Total') }}</span>
                                                </span>
                                            </strong>
                                            <span class="ms-2" id="abDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('VAT (7%)') }}</strong>
                                            <span class="ms-2" id="vatDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 bg-light">
                                            <strong>{{ __('Total Price') }}:</strong>
                                            <span class="ms-2" id="finalDisplay">0.00</span>
                                        </div>

                                        <!-- Hidden inputs for backend -->
                                        <input type="hidden" id="misc_total" name="misc_total">
                                        <input type="hidden" id="operating_expenses" name="operating_expenses">
                                        <input type="hidden" id="category_ab_total" name="category_ab_total">
                                        <input type="hidden" id="vat_total" name="vat_total">
                                        <input type="hidden" id="final_total" name="final_total">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ===== Mobile View Only (NEW - shows below rows) ===== -->
                <div class="col-12 mobile-photo-section">
                    <!-- Mobile Add Row Button - Standalone (no grid/container) -->
                    <div class="mobile-add-row-only">
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg" id="add-row-btn-mobile">
                                <i class="fas fa-plus-circle me-2"></i>{{ __('Add New Item') }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Mobile Photo Upload Section -->
                    <div class="mobile-photo-upload">
                        <div class="form-section">
                            <div class="card shadow-sm">
                                <div class="btn_div card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ __('Project Photos') }}</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Existing Photos List -->
                                    @if($home->images->count() > 0)
                                        <div class="existing-photos-list mb-3" id="existing-photos-list" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">{{ __('Existing Photos') }}</small>
                                                <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllPhotosInEdit()" title="{{ __('Delete All Photos') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @foreach($home->images as $image)
                                                @php
                                                    $isDeleted = isset($deletedPhotoIds) && in_array($image->id, $deletedPhotoIds);
                                                @endphp
                                                <div class="photo-list-item existing-photo {{ $isDeleted ? 'deleted' : '' }}" 
                                                     data-photo-id="{{ $image->id }}"
                                                     style="{{ $isDeleted ? 'opacity: 0.5; text-decoration: line-through;' : '' }}">
                                                    <div class="photo-name" title="{{ basename($image->image_path) }}">
                                                        <i class="fas fa-image me-1 text-success"></i>
                                                        {{ basename($image->image_path) }}
                                                    </div>
                                                    <div class="photo-size">Existing</div>
                                                </div>
                                                @if($isDeleted)
                                                    <input type="hidden" name="delete_photos[]" value="{{ $image->id }}" class="delete-photo-input">
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="photo-upload-area" id="photo-upload-area">
                                        <div class="photo-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h5>{{ __('Upload Project Photos') }}</h5>
                                        <p class="text-muted">{{ __('Drag & drop photos here or click to browse') }}</p>
                                        <p class="text-muted small mb-2">{{ __('Supported formats: JPG, PNG, GIF, WebP, HEIC, AVIF, BMP, TIFF, SVG, RAW formats. Max 50MB per photo') }}</p>
                                        
                                        <!-- Photo counter -->
                                        <div class="photo-counter" id="photo-counter">
                                            <i class="fas fa-images me-1"></i>
                                            <span id="photo-count">{{ $home->images->count() + (isset($restoredPhotos) ? count($restoredPhotos) : 0) }}</span> {{ __('photos selected') }}
                                        </div>
                                        
                                        <!-- Delete All Photos Button -->
                                        <div class="d-flex justify-content-end mb-2">
                                            <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllPhotosInEdit()" title="{{ __('Delete All Photos') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- New photos list (hidden) -->
                                        <div class="new-photos-section" id="new-photos-section" style="display: none;">
                                            <div class="photo-list" id="photo-list" style="display: none;"></div>
                                        </div>
                                        
                                        <input type="file" id="photo-input" name="photos[]" multiple accept="image/*,.heic,.heif,.avif,.cr2,.nef,.arw,.dng,.raw,.orf,.rw2,.pef,.sr2,.raf" style="display: none;">
                                        <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('photo-input').click()">
                                            <i class="fas fa-folder-open me-2"></i>{{ __('Browse Photos') }}
                                        </button>
                                    </div>
                                    
                                    <!-- Upload Progress -->
                                    <div class="upload-loading" id="upload-loading">
                                        <div class="spinner-border text-primary" role="status">
                                        </div>
                                        <div class="upload-progress">
                                            <div class="upload-progress-bar" id="upload-progress-bar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cost Summary -->
                    <div class="mobile-cost-summary">
                        <div class="form-section">
                            <div class="card shadow-sm">
                                <div class="btn_div card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>{{ __('Cost Summary') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="summary-row">
                                        <div class="summary-label">{{ __('Total') }}:</div>
                                        <div class="summary-value" id="miscDisplayMobile">0.00</div>
                                    </div>
                                    <div class="summary-row">
                                        <div class="summary-label">{{ __('Operating + Profit (15%)') }}:</div>
                                        <div class="summary-value" id="operatingDisplayMobile">0.00</div>
                                    </div>
                                    <div class="summary-row">
                                        <div class="summary-label">{{ __('Category A,B Total') }}:</div>
                                        <div class="summary-value" id="abDisplayMobile">0.00</div>
                                    </div>
                                    <div class="summary-row">
                                        <div class="summary-label">{{ __('VAT (7%)') }}:</div>
                                        <div class="summary-value" id="vatDisplayMobile">0.00</div>
                                    </div>
                                    <div class="summary-row total-row">
                                        <div class="summary-label" style="font-size:18px; color: #28a745;">{{ __('Total Price') }}:</div>
                                        <div class="summary-value total-value" id="finalDisplayMobile">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="mobile-action-buttons">
                        <div class="form-section">
                            <h5 class="btn_div form-section-title">
                                <i class="fas fa-tasks me-2"></i>{{ __('Actions') }}
                            </h5>
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="btn-group-vertical w-100" role="group">
                                        <button type="button" class="btn btn-primary btn-lg mb-3" id="preview-btn-mobile">
                                            <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                                        </button>
                                        
                                        <button type="submit" class="btn btn-success btn-lg mb-3" id="generate-btn-mobile">
                                            <i class="fas fa-save me-2"></i>{{ __('Update') }}
                                        </button>
                                        
                                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg" onclick="clearEditSessionOnCancel()">
                                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                                        </a>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ __('Click Preview to review before updating') }}
                                        </small> <br>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ __('Do not click Preview after adding new photos') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- iPad-specific 2-column layout -->
                    <div class="ipad-layout">
                        <!-- Left Column: Add Row + Photo Upload -->
                        <div class="ipad-left-column">
                            <!-- iPad Add Row Button - Standalone Above Photo Section -->
                            <div class="ipad-add-row-above-photo">
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-lg ipad-add-row-btn" id="add-row-btn-ipad">
                                        <i class="fas fa-plus-circle me-2"></i>{{ __('Add New Item') }}
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Photo Upload Section -->
                            <div class="form-section">
                                <div class="card shadow-sm">
                                    <div class="btn_div card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ __('Project Photos') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Existing Photos List -->
                                        @if($home->images->count() > 0)
                                            <div class="existing-photos-list mb-3" id="existing-photos-list" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">{{ __('Existing Photos') }}</small>
                                                    <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllPhotosInEdit()" title="{{ __('Delete All Photos') }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                @foreach($home->images as $image)
                                                    @php
                                                        $isDeleted = isset($deletedPhotoIds) && in_array($image->id, $deletedPhotoIds);
                                                    @endphp
                                                    <div class="photo-list-item existing-photo {{ $isDeleted ? 'deleted' : '' }}" 
                                                         data-photo-id="{{ $image->id }}"
                                                         style="{{ $isDeleted ? 'opacity: 0.5; text-decoration: line-through;' : '' }}">
                                                        <div class="photo-name" title="{{ basename($image->image_path) }}">
                                                            <i class="fas fa-image me-1 text-success"></i>
                                                            {{ basename($image->image_path) }}
                                                        </div>
                                                        <div class="photo-size">Existing</div>
                                                    </div>
                                                    @if($isDeleted)
                                                        <input type="hidden" name="delete_photos[]" value="{{ $image->id }}" class="delete-photo-input">
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="photo-upload-area" id="photo-upload-area">
                                            <div class="photo-upload-icon">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                            <h5>{{ __('Upload Project Photos') }}</h5>
                                            <p class="text-muted">{{ __('Drag & drop photos here or click to browse') }}</p>
                                            <p class="text-muted small mb-2">{{ __('Supported formats: JPG, PNG, GIF, WebP, HEIC, AVIF, BMP, TIFF, SVG, RAW formats. Max 50MB per photo') }}</p>
                                            
                                            <!-- Photo counter -->
                                            <div class="photo-counter" id="photo-counter">
                                                <i class="fas fa-images me-1"></i>
                                                <span id="photo-count">{{ $home->images->count() + (isset($restoredPhotos) ? count($restoredPhotos) : 0) }}</span> {{ __('photos selected') }}
                                            </div>
                                            
                                            <!-- Delete All Photos Button -->
                                            <div class="d-flex justify-content-end mb-2">
                                                <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllPhotosInEdit()" title="{{ __('Delete All Photos') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                            <!-- New photos list (hidden) -->
                                            <div class="new-photos-section" id="new-photos-section" style="display: none;">
                                                <div class="photo-list" id="photo-list" style="display: none;"></div>
                                            </div>
                                            
                                            <input type="file" id="photo-input" name="photos[]" multiple accept="image/*,.heic,.heif,.avif,.cr2,.nef,.arw,.dng,.raw,.orf,.rw2,.pef,.sr2,.raf" style="display: none;">
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('photo-input').click()">
                                                <i class="fas fa-folder-open me-2"></i>{{ __('Browse Photos') }}
                                            </button>
                                        </div>
                                        
                                        <!-- Upload Progress -->
                                        <div class="upload-loading" id="upload-loading">
                                            <div class="spinner-border text-primary" role="status">
                                            </div>
                                            <div class="upload-progress">
                                                <div class="upload-progress-bar" id="upload-progress-bar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Summary + Actions Only -->
                        <div class="ipad-right-column">
                            <!-- 1. Calculation Summary -->
                            <div class="form-section">
                                <div class="card shadow-sm">
                                    <div class="btn_div card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>{{ __('Cost Summary') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('Total') }}:</div>
                                            <div class="summary-value" id="miscDisplayMobile">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('Operating + Profit (15%)') }}:</div>
                                            <div class="summary-value" id="operatingDisplayMobile">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('Category A,B Total') }}:</div>
                                            <div class="summary-value" id="abDisplayMobile">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('VAT (7%)') }}:</div>
                                            <div class="summary-value" id="vatDisplayMobile">0.00</div>
                                        </div>
                                        <div class="summary-row total-row">
                                            <div class="summary-label" style="font-size:18px; color: #28a745;">{{ __('Total Price') }}:</div>
                                            <div class="summary-value total-value" id="finalDisplayMobile">0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Action Buttons -->
                            <div class="form-section">
                                <h5 class="btn_div form-section-title">
                                    <i class="fas fa-tasks me-2"></i>{{ __('Actions') }}
                                </h5>
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="btn-group-vertical w-100" role="group">
                                            <button type="button" class="btn btn-primary btn-lg mb-3" id="preview-btn-mobile">
                                                <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                                            </button>
                                            
                                            <button type="submit" class="btn btn-success btn-lg mb-3" id="generate-btn-mobile">
                                                <i class="fas fa-save me-2"></i>{{ __('Update') }}
                                            </button>
                                            
                                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg" onclick="clearEditSessionOnCancel()">
                                                <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                                            </a>
                                        </div>
                                        
                                        <div class="mt-3 text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ __('Click Preview to review before updating') }}
                                            </small> <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ __('Do not click Preview after adding new photos') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ===== /Mobile View Only ===== -->

            </div>{{-- /row --}}
            <div id="fab-btn" class="fab-btn">
                <i id="fab-icon" class="fas fa-plus"></i>
            </div>
            
            <!-- iPad Floating Add Row Button -->
            <div id="ipad-add-row-fab" class="ipad-add-row-fab">
                <i class="fas fa-plus"></i>
                <span class="fab-text">{{ __('Add Row') }}</span>
            </div>
        </div>{{-- /xp-contentbar --}}
    </form>

    {{-- Template for new rows (no id) --}}
    <template id="row-template">
        <div class="card m-b-2 item-row" data-row-index="__INDEX__">
            <div class="card-header bg-white">
                <div class="row g-3 g-compact align-items-end">
                    <input type="hidden" name="details[__INDEX__][id]" value="">
                    <input type="hidden" name="details[__INDEX__][_delete]" value="0" class="js-delete-flag">
                    <div class="col-3 col-sm-2 col-md-1 field-col">
                        <label class="form-label">{{ __('No') }}</label>
                        <div class="input-group input-42">
                            <input type="number" class="form-control readonly-input serial" name="details[__INDEX__][no]"
                                value="__SER__" min="1" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 field-col">
                        <label class="form-label">{{ __('Category') }}</label>
                        <div class="input-group input-42">
                            <input type="text" class="form-control" name="details[__INDEX__][category_name]">
                            <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)"
                                title="{{ __('Speak') }}">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-6 col-md-2 field-col">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <div class="input-group input-42">
                            <input type="text" step="0.01" class="form-control js-amount"
                                name="details[__INDEX__][amount]" placeholder=".00">
                        </div>
                    </div>
                    <div class="col-6 col-md-2 field-col">
                        <label class="form-label">{{ __('Unit') }}</label>
                        <div class="input-group input-42">
                            <select class="form-control units" name="details[__INDEX__][unit]">
                            <option value="" selected disabled>{{ __('Select Unit') }}</option>
                            <option value="sq.m">sq.m</option>
                            <option value="m">m</option>
                            <option value="lump sum">lump sum</option>
                            <option value="leaf">leaf</option>
                            <option value="trip">trip</option>
                            <option value="point">point</option>
                            <option value="day">day</option>
                            <option value="piece">piece</option>
                            <option value="floor">floor</option>
                            <option value="set">set</option>
                            <option value="sheet">sheet</option>
                            <option value="unit">unit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 field-col">
                        <label class="form-label">{{ __('Material Price') }}</label>
                        <div class="input-group input-42">
                            <input type="text" step="0.01" class="form-control js-mc" name="details[__INDEX__][mc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-6 col-md-2 field-col">
                        <label class="form-label">{{ __('Material Total') }}</label>
                        <div class="input-group input-42">
                            <input type="text" class="form-control readonly-input js-mat-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                    <div class="col-md-6"></div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Labor Price') }}</label>
                        <div class="input-group input-42">
                            <input type="text" step="0.01" class="form-control js-lc" name="details[__INDEX__][lc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Labor Total') }}</label>
                        <div class="input-group input-42">
                            <input type="text" class="form-control readonly-input js-lab-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Grand Total') }}</label>
                        <div class="input-group input-42">
                            <input type="text" class="form-control readonly-input js-grand-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-danger remove-row"
                            onclick="markRowDeleted(this)">&times;</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/plugins/validations/homeValidation.js') }}"></script>
    <script src="{{ asset('assets/plugins/home/home-form.js') }}"></script>
    <script src="{{ asset('assets/js/chunked-upload.js') }}"></script>
    <script>
        // Restore form data when coming back from preview
        document.addEventListener('DOMContentLoaded', function() {
            // Restore form details
            const restoredData = @json($restoredData ?? null);
            const restoredPhotos = @json($restoredPhotos ?? []);
            
            if (restoredData && restoredData.details) {
                // Restore form field values
                Object.keys(restoredData).forEach(key => {
                    if (key !== 'details') {
                        const input = document.querySelector(`[name="${key}"]`);
                        if (input && restoredData[key]) {
                            input.value = restoredData[key];
                        }
                    }
                });
                
                // Restore details rows
                const details = restoredData.details || [];
                details.forEach((detail, index) => {
                    setTimeout(() => {
                        // Find or create row
                        let row = document.querySelector(`[data-row-index="${index}"]`);
                        if (!row && typeof window.addRow === 'function') {
                            window.addRow();
                            row = document.querySelector(`[data-row-index="${index}"]`);
                        }
                        
                        if (row) {
                            // Populate row fields
                            Object.keys(detail).forEach(fieldName => {
                                if (fieldName !== '_delete' && fieldName !== 'id') {
                                    const input = row.querySelector(`[name*="[${fieldName}]"]`);
                                    if (input && detail[fieldName] !== null && detail[fieldName] !== undefined) {
                                        input.value = detail[fieldName];
                                    }
                                }
                            });
                            
                            // Trigger calculation
                            const amountInput = row.querySelector('input[name*="[amount]"]');
                            if (amountInput) amountInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }, index * 100); // Stagger the population
                });
            }
            
            // Restore photos (but exclude deleted ones)
            const deletedPhotoIds = @json($deletedPhotoIds ?? []);
            const restoredPhotosFromSession = @json($restoredPhotos ?? []);
            
            console.log('Restoring photos from session:', {
                restoredPhotosCount: restoredPhotosFromSession.length,
                restoredPhotos: restoredPhotosFromSession,
                deletedPhotoIds: deletedPhotoIds
            });
            
            if (restoredPhotosFromSession && restoredPhotosFromSession.length > 0) {
                const photoCountElement = document.getElementById('photo-count');
                const photoList = document.getElementById('photo-list');
                const newPhotosSection = document.getElementById('new-photos-section');
                
                // Create a list of restored photos for display (only if not deleted)
                if (photoList) {
                    restoredPhotosFromSession.forEach((photoPath, index) => {
                        // Check if this photo is already in the list
                        const existingItem = photoList.querySelector(`[data-photo-path="${photoPath}"], [data-temp-path="${photoPath}"]`);
                        if (existingItem) {
                            console.log('Photo already in list, skipping:', photoPath);
                            return; // Skip if already exists
                        }
                        
                        const photoItem = document.createElement('div');
                        photoItem.className = 'photo-list-item restored-photo';
                        photoItem.dataset.photoPath = photoPath;
                        photoItem.dataset.tempPath = photoPath; // Also set tempPath for consistency
                        
                        // Extract filename from path
                        const filename = photoPath.split('/').pop();
                        
                        photoItem.innerHTML = `
                            <div class="photo-name" title="Restored Photo: ${filename}">
                                <i class="fas fa-image me-1 text-warning"></i>
                                ${filename}
                                <span class="format-badge badge-apple">RESTORED</span>
                            </div>
                            <div class="photo-size">Restored</div>
                            <button type="button" class="photo-remove" onclick="removeRestoredPhoto(this)" title="Delete All Restored Photos">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        photoList.appendChild(photoItem);
                        console.log('Added restored photo to list:', filename);
                    });
                }
                
                // Create hidden inputs for restored photos (avoid duplicates, but only if not deleted)
                const form = document.getElementById('home-form');
                if (form) {
                    restoredPhotosFromSession.forEach((photoPath, index) => {
                        // Check if input already exists
                        const existingInput = form.querySelector(`input[name="restored_photos[]"][value="${photoPath}"]`);
                        if (existingInput) {
                            console.log('Hidden input already exists for:', photoPath);
                            return; // Skip if already exists
                        }
                        
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'restored_photos[]';
                        hiddenInput.value = photoPath;
                        hiddenInput.className = 'restored-photo-input';
                        form.appendChild(hiddenInput);
                        console.log('Created hidden input for restored photo:', photoPath);
                    });
                    
                    // Log all restored photo inputs
                    const allRestoredInputs = Array.from(form.querySelectorAll('input[name="restored_photos[]"]')).map(input => input.value);
                    console.log('All restored_photos[] inputs after restoration:', allRestoredInputs.length, allRestoredInputs);
                }
                
                // Update photo counter after restoring
                if (photoCountElement && typeof updatePhotoCounter === 'function') {
                    updatePhotoCounter();
                } else if (photoCountElement) {
                    // Fallback if updatePhotoCounter is not available yet
                    const existingCount = document.querySelectorAll('.existing-photo:not(.deleted)').length;
                    const uploadedCount = document.querySelectorAll('.uploaded-photo:not(.deleted)').length;
                    const restoredCount = document.querySelectorAll('.restored-photo:not(.deleted)').length;
                    photoCountElement.textContent = existingCount + uploadedCount + restoredCount;
                    console.log('Photo counter updated (fallback):', {
                        existing: existingCount,
                        uploaded: uploadedCount,
                        restored: restoredCount,
                        total: existingCount + uploadedCount + restoredCount
                    });
                }
            }
            
            // Restore deleted photo IDs from session (so they stay deleted when coming back from preview)
            if (deletedPhotoIds && deletedPhotoIds.length > 0) {
                const form = document.getElementById('home-form');
                if (form) {
                    deletedPhotoIds.forEach(photoId => {
                        // Check if delete input already exists
                        const existingDeleteInput = form.querySelector(`input[name="delete_photos[]"][value="${photoId}"]`);
                        if (!existingDeleteInput) {
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name = 'delete_photos[]';
                            deleteInput.value = photoId;
                            deleteInput.className = 'delete-photo-input';
                            form.appendChild(deleteInput);
                        }
                    });
                }
            }
            
            // Function to remove restored photos - now removes ALL restored photos
            window.removeRestoredPhoto = function(button) {
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("Delete all restored photos? This cannot be undone.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("Yes, delete all!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const allRestoredPhotos = document.querySelectorAll('.photo-list-item.restored-photo');
                        
                        allRestoredPhotos.forEach(photoItem => {
                            const photoPath = photoItem.dataset.photoPath;
                            
                            // Remove from display
                            photoItem.remove();
                            
                            // Remove corresponding hidden input
                            const hiddenInput = document.querySelector(`input[name="restored_photos[]"][value="${photoPath}"]`);
                            if (hiddenInput) {
                                hiddenInput.remove();
                            }
                        });
                        
                        // Update counter
                        const photoCountElement = document.getElementById('photo-count');
                        if (photoCountElement) {
                            // Recalculate total count
                            const existingPhotosCount = document.querySelectorAll('.existing-photo').length;
                            const restoredPhotosCount = 0; // All restored photos deleted
                            const newPhotosCount = document.querySelectorAll('.photo-list-item:not(.existing-photo):not(.restored-photo)').length;
                            const totalCount = existingPhotosCount + restoredPhotosCount + newPhotosCount;
                            
                            photoCountElement.textContent = totalCount;
                        }
                        
                        // Hide new photos section if no photos left
                        const photoList = document.getElementById('photo-list');
                        const newPhotosSection = document.getElementById('new-photos-section');
                        if (photoList && newPhotosSection) {
                            const hasPhotos = photoList.querySelectorAll('.photo-list-item').length > 0;
                            if (!hasPhotos) {
                                // Keep section hidden - UI modification to hide photo list
                                // newPhotosSection.style.display = 'none';
                            }
                        }

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Deleted!") }}',
                            text: '{{ __("All restored photos have been deleted.") }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            };
            
            // Function to clear session and refresh page
            window.clearSessionAndRefresh = function() {
                Swal.fire({
                    title: '{{ __("Clear Session?") }}',
                    text: '{{ __("This will clear all temporary photos and session data, then refresh the page.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f39c12',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("Yes, clear it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: '{{ __("Clearing...") }}',
                            text: '{{ __("Please wait while we clear the session data.") }}',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Clear session via AJAX
                        fetch('{{ route("home.clear-session") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                clear_session: true
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Refresh the page without restore parameter
                                window.location.href = window.location.pathname;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error") }}',
                                    text: data.message || '{{ __("Failed to clear session") }}'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error") }}',
                                text: '{{ __("An error occurred while clearing the session") }}'
                            });
                        });
                    }
                });
            };
            
            // Initialize chunked upload for edit form
            if (typeof initializeChunkedUpload === 'function') {
                initializeChunkedUpload();
            }
            
            // Initialize photo counter on page load
            const photoCountElement = document.getElementById('photo-count');
            if (photoCountElement && typeof updatePhotoCounter === 'function') {
                updatePhotoCounter();
            } else if (photoCountElement) {
                // Fallback initialization
                const existingCount = document.querySelectorAll('.existing-photo:not(.deleted)').length;
                const uploadedCount = document.querySelectorAll('.uploaded-photo:not(.deleted)').length;
                const restoredCount = document.querySelectorAll('.restored-photo:not(.deleted)').length;
                photoCountElement.textContent = existingCount + uploadedCount + restoredCount;
            }
            
            // Function to clear session when cancel is clicked
            window.clearEditSessionOnCancel = function() {
                // Clear preview session data when canceling (not saving)
                fetch('{{ route("home.clear-session") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        clear_session: true
                    })
                }).catch(error => {
                    console.warn('Failed to clear session:', error);
                });
                // Continue with navigation
                return true;
            };
            
            // Unified function to delete ALL photos (existing + new + restored)
            window.deleteAllPhotosInEdit = function() {
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: '{{ __("Delete all photos? . They will be permanently deleted when you click Update.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("Yes, delete all!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('home-form');
                        if (!form) return;
                        
                        // 1. Mark all existing photos for deletion
                        const existingPhotos = document.querySelectorAll('.photo-list-item.existing-photo:not(.deleted)');
                        existingPhotos.forEach(photoItem => {
                            const photoId = photoItem.dataset.photoId;
                            
                            if (photoId) {
                                // Check if delete input already exists
                                const existingDeleteInput = form.querySelector(`input[name="delete_photos[]"][value="${photoId}"]`);
                                if (!existingDeleteInput) {
                                    const deleteInput = document.createElement('input');
                                    deleteInput.type = 'hidden';
                                    deleteInput.name = 'delete_photos[]';
                                    deleteInput.value = photoId;
                                    deleteInput.className = 'delete-photo-input';
                                    form.appendChild(deleteInput);
                                }
                                
                                // Mark as deleted visually
                                photoItem.classList.add('deleted');
                                photoItem.style.opacity = '0.5';
                                photoItem.style.textDecoration = 'line-through';
                            }
                        });
                        
                        // 2. Remove all new/uploaded photos (they're not saved yet, so just remove them)
                        const uploadedPhotos = document.querySelectorAll('.photo-list-item.uploaded-photo:not(.deleted)');
                        uploadedPhotos.forEach(photoItem => {
                            const tempPath = photoItem.dataset.tempPath;
                            
                            // Remove from display
                            photoItem.classList.add('deleted');
                            photoItem.style.opacity = '0.5';
                            photoItem.style.textDecoration = 'line-through';
                            
                            // Remove corresponding hidden input
                            const hiddenInput = form.querySelector(`input[name="restored_photos[]"][value="${tempPath}"]`);
                            if (hiddenInput) {
                                hiddenInput.remove();
                            }
                        });
                        
                        // 3. Remove all restored photos
                        const restoredPhotos = document.querySelectorAll('.photo-list-item.restored-photo:not(.deleted)');
                        restoredPhotos.forEach(photoItem => {
                            const photoPath = photoItem.dataset.photoPath;
                            
                            // Mark as deleted visually
                            photoItem.classList.add('deleted');
                            photoItem.style.opacity = '0.5';
                            photoItem.style.textDecoration = 'line-through';
                            
                            // Remove corresponding hidden input
                            const hiddenInput = form.querySelector(`input[name="restored_photos[]"][value="${photoPath}"]`);
                            if (hiddenInput) {
                                hiddenInput.remove();
                            }
                        });
                        
                        // 4. Clear uploader queue if it exists
                        if (window.photoUploader) {
                            window.photoUploader.clear();
                        }
                        
                        // 5. Update counter
                        if (typeof updatePhotoCounter === 'function') {
                            updatePhotoCounter();
                        } else {
                            const photoCountElement = document.getElementById('photo-count');
                            if (photoCountElement) {
                                const existingCount = document.querySelectorAll('.existing-photo:not(.deleted)').length;
                                const uploadedCount = document.querySelectorAll('.uploaded-photo:not(.deleted)').length;
                                const restoredCount = document.querySelectorAll('.restored-photo:not(.deleted)').length;
                                photoCountElement.textContent = existingCount + uploadedCount + restoredCount;
                            }
                        }
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Deleted!") }}',
                            text: '{{ __("All photos have been marked for deletion. They will be permanently deleted when you click Update.") }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            };
        });
    </script>
@endpush
