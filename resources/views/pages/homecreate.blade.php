@extends('layouts.master')

@section('title', __('Generate Home Excel Form'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/home-forminput-table.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/home-common.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/photo-upload.css') }}" rel="stylesheet">
@endpush

@section('content')
    <form id="home-form" action="{{ route('home.store') }}" method="POST" novalidate enctype="multipart/form-data">
        @csrf
        <div class="xp-contentbar">
            <div class="row">

                <!-- Enhanced Project Information Section -->
                <div class="col-12">
                    <div class="project-info-container">
                        <div class="project-info-title">
                            <h2>{{ __('Project Information') }}</h2>
                            <p>{{ __('Please fill in the details for your home building quotation') }}</p>
                        </div>
                        
                        <div class="project-info-grid">
                            <!-- Project Name -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-building"></i>
                                        </span>
                                        {{ __('Project Name') }}
                                        <span class="required-star">*</span>
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="project_name" id="project_name"
                                            placeholder="{{ __('Enter project name') }}" 
                                            value="{{ old('project_name', $restoredData['project_name'] ?? '') }}" required
                                            data-error-required="{{ __('Project name is required') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('project_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- List Name -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-list"></i>
                                        </span>
                                        {{ __('List Name') }}
                                        <span class="required-star">*</span>
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="list_name" id="list_name" 
                                            placeholder="{{ __('Enter list name') }}"
                                            value="{{ old('list_name', $restoredData['list_name'] ?? '') }}" required
                                            data-error-required="{{ __('List name is required') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('list_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Client Name (Dear) -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        {{ __('Client Name') }}
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="dear" id="dear" 
                                            placeholder="{{ __('Enter client name') }}"
                                            value="{{ old('dear', $restoredData['dear'] ?? '') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('dear') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- House Number -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-home"></i>
                                        </span>
                                        {{ __('House Number') }}
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="house_no" id="house_no" 
                                            placeholder="{{ __('Enter house number') }}"
                                            value="{{ old('house_no', $restoredData['house_no'] ?? '') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('house_no') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Street Address -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-road"></i>
                                        </span>
                                        {{ __('Street Address') }}
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="street" id="street" 
                                            placeholder="{{ __('Enter street address') }}"
                                            value="{{ old('street', $restoredData['street'] ?? '') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('street') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Quarter/Area -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        {{ __('Quarter/Area') }}
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="quarter" id="quarter" 
                                            placeholder="{{ __('Enter quarter or area') }}"
                                            value="{{ old('quarter', $restoredData['quarter'] ?? '') }}">
                                        <button type="button" class="mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    @error('quarter') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Construction Company (Trooper) -->
                            <div class="project-field-card">
                                <div class="project-field-header">
                                    <label>
                                        <span class="field-icon">
                                            <i class="fas fa-hard-hat"></i>
                                        </span>
                                        {{ __('Construction Company') }}
                                        <span class="required-star">*</span>
                                    </label>
                                </div>
                                <div class="project-field-body">
                                    <div class="input-group">
                                        <select name="trooper" id="trooper" class="form-control select-field" required 
                                            data-error-required="{{ __('Company is required') }}">
                                            <option value="" selected disabled>{{ __('Select Company') }}</option>
                                            <option value="{{ __('168 Home company') }}"
                                                {{ old('trooper', $restoredData['trooper'] ?? '') === __('168 Home company') ? 'selected' : '' }}>
                                                {{ __('168 Home company') }}
                                            </option>
                                            <option value="{{ __('Pi Kaew') }}"
                                                {{ old('trooper', $restoredData['trooper'] ?? '') === __('Pi Kaew') ? 'selected' : '' }}>
                                                {{ __('Pi Kaew') }}
                                            </option>
                                        </select>
                                    </div>
                                    @error('trooper') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Construction Items Section -->
                <div class="col-12">
                    <div class="items-section-container">
                        <div class="items-section-title">
                            <h2>{{ __('Construction Items') }}</h2>
                            <p>{{ __('Add all construction materials and labor costs for your project') }}</p>
                        </div>
                        
                        <div id="rows-container">
                            <div class="item-row-card card m-b-20 item-row">
                                <div class="item-row-header card-header bg-white">
                                    <span class="item-number">{{ __('No') }} 1</span>
                                    <button type="button" class="remove-item-btn remove-row">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Remove') }}
                                    </button>
                                </div>
                                
                                <div class="item-row-body card-body">
                                    <div class="items-table-layout">
                                        <!-- Category -->
                                        <div class="items-field-col category-col">
                                            <label>{{ __('Category') }} <span class="required-star">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    name="details[0][category_name]"
                                                    placeholder="{{ __('Category A / B / ...') }}" required
                                                    data-error-required="{{ __('Category is required') }}">
                                                <button type="button" class="mic-btn"
                                                    onclick="startDictation(this)">
                                                    <i class="fas fa-microphone"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="items-field-col amount-col">
                                            <label>{{ __('Amount') }} <span class="required-star">*</span></label>
                                            <input type="text" step="0.01" class="form-control js-amount"
                                                name="details[0][amount]" placeholder=".00" required
                                                data-error-required="{{ __('Amount is required') }}"
                                                data-error-number="{{ __('Please enter number only') }}">
                                        </div>

                                        <!-- Unit -->
                                        <div class="items-field-col unit-col">
                                            <label>{{ __('Unit') }} <span class="required-star">*</span></label>
                                            <select class="form-select units" name="details[0][unit]" required
                                                data-error-required="{{ __('Unit is required') }}">
                                                <option value="" selected disabled>{{ __('Select Unit') }}</option>
                                                <option value="{{ __('sq.m') }}">{{ __('sq.m') }}</option>
                                                <option value="{{ __('m') }}">{{ __('m') }}</option>
                                                <option value="{{ __('lump sum') }}">{{ __('lump sum') }}</option>
                                                <option value="{{ __('leaf') }}">{{ __('leaf') }}</option>
                                                <option value="{{ __('trip') }}">{{ __('trip') }}</option>
                                                <option value="{{ __('point') }}">{{ __('point') }}</option>
                                                <option value="{{ __('day') }}">{{ __('day') }}</option>
                                                <option value="{{ __('piece') }}">{{ __('piece') }}</option>
                                                <option value="{{ __('floor') }}">{{ __('floor') }}</option>
                                                <option value="{{ __('set') }}">{{ __('set') }}</option>
                                                <option value="{{ __('sheet') }}">{{ __('sheet') }}</option>
                                                <option value="{{ __('unit') }}">{{ __('unit') }}</option>
                                            </select>
                                        </div>

                                        <!-- Material Price -->
                                        <div class="items-field-col price-col">
                                            <label>{{ __('Material Price') }} <span class="required-star">*</span></label>
                                            <input type="text" step="0.01" class="form-control js-mc"
                                                name="details[0][mc_price]" placeholder=".00" required
                                                data-error-required="{{ __('Material cost or labor cost is required') }}"
                                                data-error-number="{{ __('Please enter number only') }}">
                                        </div>

                                        <!-- Labor Price -->
                                        <div class="items-field-col price-col">
                                            <label>{{ __('Labor Price') }} <span class="required-star">*</span></label>
                                            <input type="text" step="0.01" class="form-control js-lc"
                                                name="details[0][lc_price]" placeholder=".00" required
                                                data-error-required="{{ __('Material cost or labor cost is required') }}"
                                                data-error-number="{{ __('Please enter number only') }}">
                                        </div>

                                        <!-- Material Total -->
                                        <div class="items-field-col total-col">
                                            <label>{{ __('Material Total') }}</label>
                                            <input type="text" class="form-control readonly-input js-mat-total"
                                                placeholder="0.00" readonly>
                                        </div>

                                        <!-- Labor Total -->
                                        <div class="items-field-col total-col">
                                            <label>{{ __('Labor Total') }}</label>
                                            <input type="text" class="form-control readonly-input js-lab-total"
                                                placeholder="0.00" readonly>
                                        </div>

                                        <!-- Grand Total -->
                                        <div class="items-field-col grand-total-col">
                                            <label>{{ __('Grand Total') }}</label>
                                            <div class="grand-total-container">
                                                <input type="text" class="form-control readonly-input js-grand-total grand-total-input"
                                                    placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add New Item Button -->
                        <div class="add-item-section">
                            <button type="button" class="add-item-btn" id="add-row-btn">
                                <i class="fas fa-plus"></i>
                                {{ __('Add New Item') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Photos & Summary Section -->
                <div class="col-12">
                    <div class="photos-summary-container">
                        <div class="photos-summary-title">
                            <h2>{{ __('Project Photos & Cost Summary') }}</h2>
                            <p>{{ __('Upload project photos and review the complete cost breakdown') }}</p>
                        </div>
                        
                        <div class="photos-summary-layout">
                            <!-- Photo Upload Section -->
                            <div class="photo-upload-section">
                                <div class="photo-upload-header">
                                    <h3>
                                        <span class="section-icon">
                                            <i class="fas fa-images"></i>
                                        </span>
                                        {{ __('Project Photos') }}
                                    </h3>
                                </div>
                                <div class="photo-upload-body">
                                    <div class="photo-upload-area" id="photo-upload-area-desktop">
                                        <div class="photo-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                        <div class="photo-upload-text">{{ __('Upload Project Photos') }}</div>
                                        <div class="photo-upload-subtext">{{ __('Drag & drop photos here or click to browse') }}</div>
                                        <div class="photo-upload-subtext small">{{ __('Supported formats: JPG, PNG, GIF, WebP, HEIC, AVIF, BMP, TIFF, SVG, RAW formats. Max 50MB per photo') }}</div>
                                        
                                        <div class="photo-counter" id="photo-counter-desktop">
                                            <i class="fas fa-images me-2"></i>
                                            <span id="photo-count-desktop">0</span> {{ __('photos selected') }}
                                        </div>
                                        
                                        <div class="photo-actions">
                                            <button type="button" class="photo-btn danger" onclick="deleteAllNewPhotosAndClearSession()" title="{{ __('Delete All Photos') }}">
                                                <i class="fas fa-trash"></i>
                                                {{ __('Delete All') }}
                                            </button>
                                            <button type="button" class="photo-btn primary" onclick="document.getElementById('photo-input-desktop').click()">
                                                <i class="fas fa-folder-open"></i>
                                                {{ __('Browse Photos') }}
                                            </button>
                                        </div>
                                        
                                        <div class="new-photos-section" id="new-photos-section-desktop" style="display:none;">
                                            <div class="photo-list" id="photo-list-desktop" style="display:none;"></div>
                                        </div>
                                        <input type="file" id="photo-input-desktop" name="photos[]" multiple accept="image/*,.heic,.heif,.avif,.cr2,.nef,.arw,.dng,.raw,.orf,.rw2,.pef,.sr2,.raf" style="display:none;">
                                    </div>
                                    
                                    <div class="upload-loading" id="upload-loading-desktop">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <div class="upload-progress">
                                            <div class="upload-progress-bar" id="upload-progress-bar-desktop"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cost Summary Section -->
                            <div class="cost-summary-section">
                                <div class="cost-summary-header">
                                    <h3>
                                        <span class="section-icon">
                                            <i class="fas fa-calculator"></i>
                                        </span>
                                        {{ __('Cost Summary') }}
                                    </h3>
                                </div>
                                <div class="cost-summary-body">
                                    <div class="summary-item">
                                        <div class="summary-label">{{ __('Total') }}</div>
                                        <div class="summary-value" id="miscDisplay">0.00</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">{{ __('Operating + Profit (15%)') }}</div>
                                        <div class="summary-value" id="operatingDisplay">0.00</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">{{ __('Category A,B Total') }}</div>
                                        <div class="summary-value" id="abDisplay">0.00</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">{{ __('VAT (7%)') }}</div>
                                        <div class="summary-value" id="vatDisplay">0.00</div>
                                    </div>
                                    <div class="summary-item total">
                                        <div class="summary-label">{{ __('Total Price') }}</div>
                                        <div class="summary-value" id="finalDisplay">0.00</div>
                                    </div>
                                    
                                    <!-- Hidden inputs for form submission -->
                                    <input type="hidden" id="misc_total" name="misc_total">
                                    <input type="hidden" id="operating_expenses" name="operating_expenses">
                                    <input type="hidden" id="category_ab_total" name="category_ab_total">
                                    <input type="hidden" id="vat_total" name="vat_total">
                                    <input type="hidden" id="final_total" name="final_total">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Section -->
                        <div class="action-buttons-section">
                            <div class="action-buttons-header">
                                <h3>
                                    <span class="section-icon">
                                        <i class="fas fa-tasks"></i>
                                    </span>
                                    {{ __('Actions') }}
                                </h3>
                            </div>
                            <div class="action-buttons-body">
                                <div class="action-buttons-group">
                                    <button type="submit" class="action-btn preview" formaction="{{ route('home.preview') }}">
                                        <i class="fas fa-eye"></i>
                                        {{ __('Preview') }}
                                    </button>
                                    
                                    <button type="submit" class="action-btn create" id="generate-btn-desktop">
                                        <i class="fas fa-file-excel"></i>
                                        {{ __('Create Excel') }}
                                    </button>
                                    
                                    <button type="button" class="action-btn cancel" onclick="window.location.href='{{ route('home') }}'">
                                        <i class="fas fa-times"></i>
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Controls (Hidden - replaced by new layout) -->
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
                    
                    <!-- Mobile Photo Upload Section (phone only, hidden on iPad) -->
                    <div class="mobile-photo-upload mobile-only-photo">
                        <div class="form-section">
                            <div class="card shadow-sm">
                                <div class="btn_div card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ __('Project Photos') }}</h5>
                                </div>
                                <div class="card-body">
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
                                            <span id="photo-count">0</span> {{ __('photos selected') }}
                                        </div>
                                        
                                        <!-- Delete All Photos Button -->
                                        <div class="d-flex justify-content-end mb-2">
                                            <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllNewPhotosAndClearSession()" title="{{ __('Delete All Photos') }}">
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
                                    <div class="summary-row total-row" >
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
                                        <button type="submit" class="btn btn-primary btn-lg mb-3" formaction="{{ route('home.preview') }}">
                                            <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                                        </button>
                                        
                                        <button type="submit" class="btn btn-success btn-lg mb-3" id="generate-btn-mobile">
                                            <i class="fas fa-file-excel me-2"></i>{{ __('Create') }}
                                        </button>
                                        
                                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                                        </a>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ __('Click Preview to review before generating') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- iPad-specific 2-column layout -->
                    <div class="ipad-layout">
                        <!-- Left Column: Add Row + Photo Upload (iPad only) -->
                        <div class="ipad-left-column">
                            <!-- iPad Add Row Button -->
                            <div class="ipad-add-row-above-photo">
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-lg ipad-add-row-btn" id="add-row-btn-ipad">
                                        <i class="fas fa-plus-circle me-2"></i>{{ __('Add New Item') }}
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Photo Upload Section (iPad only — same IDs as phone, shown here via CSS) -->
                            <div class="ipad-photo-upload">
                                <div class="form-section">
                                    <div class="card shadow-sm">
                                        <div class="btn_div card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ __('Project Photos') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="photo-upload-area" id="photo-upload-area-ipad">
                                                <div class="photo-upload-icon">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                </div>
                                                <h5>{{ __('Upload Project Photos') }}</h5>
                                                <p class="text-muted">{{ __('Drag & drop photos here or click to browse') }}</p>
                                                <p class="text-muted small mb-2">{{ __('Supported formats: JPG, PNG, GIF, WebP, HEIC, AVIF, BMP, TIFF, SVG, RAW formats. Max 50MB per photo') }}</p>
                                                
                                                <div class="photo-counter" id="photo-counter-ipad">
                                                    <i class="fas fa-images me-1"></i>
                                                    <span id="photo-count-ipad">0</span> {{ __('photos selected') }}
                                                </div>
                                                
                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" class="photo-remove delete-all-btn" onclick="deleteAllNewPhotosAndClearSession()" title="{{ __('Delete All Photos') }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <div class="new-photos-section" id="new-photos-section-ipad" style="display: none;">
                                                    <div class="photo-list" id="photo-list-ipad" style="display: none;"></div>
                                                </div>
                                                
                                                <input type="file" id="photo-input-ipad" name="photos[]" multiple accept="image/*,.heic,.heif,.avif,.cr2,.nef,.arw,.dng,.raw,.orf,.rw2,.pef,.sr2,.raf" style="display: none;">
                                                <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('photo-input-ipad').click()">
                                                    <i class="fas fa-folder-open me-2"></i>{{ __('Browse Photos') }}
                                                </button>
                                            </div>
                                            
                                            <div class="upload-loading" id="upload-loading-ipad">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <div class="upload-progress">
                                                    <div class="upload-progress-bar" id="upload-progress-bar-ipad"></div>
                                                </div>
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
                                            <div class="summary-value" id="miscDisplayIpad">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('Operating + Profit (15%)') }}:</div>
                                            <div class="summary-value" id="operatingDisplayIpad">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('Category A,B Total') }}:</div>
                                            <div class="summary-value" id="abDisplayIpad">0.00</div>
                                        </div>
                                        <div class="summary-row">
                                            <div class="summary-label">{{ __('VAT (7%)') }}:</div>
                                            <div class="summary-value" id="vatDisplayIpad">0.00</div>
                                        </div>
                                        <div class="summary-row total-row" >
                                            <div class="summary-label" style="font-size:18px; color: #28a745;">{{ __('Total Price') }}:</div>
                                            <div class="summary-value total-value" id="finalDisplayIpad">0.00</div>
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
                                            <button type="submit" class="btn btn-primary btn-lg mb-3" formaction="{{ route('home.preview') }}">
                                                <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                                            </button>
                                            
                                            <button type="submit" class="btn btn-success btn-lg mb-3" id="generate-btn-mobile">
                                                <i class="fas fa-file-excel me-2"></i>{{ __('Create') }}
                                            </button>
                                            
                                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                                                <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                                            </a>
                                        </div>
                                        
                                        <div class="mt-3 text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ __('Click Preview to review before generating') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ===== /Mobile View Only ===== -->
            </div>
                <div id="fab-btn" class="fab-btn">
                    <i id="fab-icon" class="fas fa-arrow-down"></i>
                </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/plugins/validations/homeValidation.js') }}"></script>
<script src="{{ asset('assets/plugins/home/home-form.js') }}"></script>
<script src="{{ asset('assets/js/chunked-upload.js') }}"></script>

<script>
// Handle desktop button
const desktopAddBtn = document.getElementById('add-row-btn');
if (desktopAddBtn) {
    desktopAddBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof addRow === 'function') {
            addRow();
        } else {
            console.error('addRow function not available');
        }
    });
}

// Handle mobile button
const mobileAddBtn = document.getElementById('add-row-btn-mobile');
if (mobileAddBtn) {
    mobileAddBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof addRow === 'function') {
            addRow();
        } else {
            console.error('addRow function not available');
        }
    });
}

// Handle iPad grid button
const iPadGridAddBtn = document.getElementById('add-row-btn-ipad');
if (iPadGridAddBtn) {
    iPadGridAddBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof addRow === 'function') {
            addRow();
        } else {
            console.error('addRow function not available');
        }
    });
}

// Sync iPad photo counter from phone counter on any change
document.addEventListener('DOMContentLoaded', function() {
    const phoneCount = document.getElementById('photo-count');
    const ipadCount  = document.getElementById('photo-count-ipad');
    if (!phoneCount || !ipadCount) return;

    const observer = new MutationObserver(function() {
        ipadCount.textContent = phoneCount.textContent;
    });
    observer.observe(phoneCount, { childList: true, characterData: true, subtree: true });
    
    // Only disable auto-save restore prompt, preserve photo data
    const autoSaveData = localStorage.getItem('home_form_autosave');
    if (autoSaveData) {
        const parsed = JSON.parse(autoSaveData);
        if (parsed.data && parsed.data.photos) {
            // Keep photos but remove form fields to prevent restore prompt
            parsed.data.details = [];
            parsed.data.project_name = '';
            parsed.data.list_name = '';
            parsed.data.dear = '';
            parsed.data.house_no = '';
            parsed.data.street = '';
            parsed.data.quarter = '';
            parsed.data.trooper = '';
            localStorage.setItem('home_form_autosave', JSON.stringify(parsed));
        } else {
            localStorage.removeItem('home_form_autosave');
        }
    }
});

// Handle iPad grid button
const iPadGridAddBtn = document.getElementById('add-row-btn-ipad');
if (iPadGridAddBtn) {
    iPadGridAddBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof addRow === 'function') {
            addRow();
        } else {
            console.error('addRow function not available');
        }
    });
}
</script>

@if(isset($restoredData) && $restoredData)
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Starting form restoration...');
    
    // Disable auto-save during restoration to prevent conflicts
    if (typeof clearAutoSave === 'function') {
        clearAutoSave();
    }
    
    // Restore form details
    const restoredDetails = @json($restoredData['details'] ?? []);
    const restoredPhotos = @json($restoredPhotos ?? []);
    
    console.log('Restored details:', restoredDetails);
    console.log('Restored photos:', restoredPhotos);
    
    if (restoredDetails && restoredDetails.length > 0) {
        console.log('Restoring', restoredDetails.length, 'detail rows');
        
        // Clear existing rows except the first one
        const rowsContainer = document.getElementById('rows-container');
        const existingRows = rowsContainer.querySelectorAll('.item-row');
        
        // Remove all rows except the first
        for (let i = 1; i < existingRows.length; i++) {
            existingRows[i].remove();
        }
        
        // Restore each detail row
        restoredDetails.forEach((detail, index) => {
            console.log('Restoring detail row', index, detail);
            
            if (index > 0) {
                // Add new row for details beyond the first
                if (typeof window.addRow === 'function') {
                    window.addRow();
                } else {
                    console.error('addRow function not available');
                }
            }
            
            // Wait a bit for the row to be added, then populate it
            setTimeout(() => {
                const row = rowsContainer.children[index];
                if (row) {
                    console.log('Populating row', index);
                    
                    // Populate the row with restored data
                    const categoryInput = row.querySelector('input[name*="[category_name]"]');
                    const amountInput = row.querySelector('input[name*="[amount]"]');
                    const unitSelect = row.querySelector('select[name*="[unit]"]');
                    const mcPriceInput = row.querySelector('input[name*="[mc_price]"]');
                    const lcPriceInput = row.querySelector('input[name*="[lc_price]"]');
                    
                    if (categoryInput) categoryInput.value = detail.category_name || '';
                    if (amountInput) amountInput.value = detail.amount || '';
                    if (unitSelect) unitSelect.value = detail.unit || '';
                    if (mcPriceInput) mcPriceInput.value = detail.mc_price || '';
                    if (lcPriceInput) lcPriceInput.value = detail.lc_price || '';
                    
                    // Trigger calculation
                    if (amountInput) amountInput.dispatchEvent(new Event('input', { bubbles: true }));
                } else {
                    console.error('Row not found for index', index);
                }
            }, index * 100); // Stagger the population
        });
    }
    
    // Restore photos
    if (restoredPhotos && restoredPhotos.length > 0) {
        const photoCountElement = document.getElementById('photo-count');
        const photoList = document.getElementById('photo-list');
        const newPhotosSection = document.getElementById('new-photos-section');
        
        if (photoCountElement) {
            photoCountElement.textContent = restoredPhotos.length;
        }
        
        // Show new photos section if we have restored photos
        if (newPhotosSection && restoredPhotos.length > 0) {
            // Keep section hidden - UI modification to hide photo list
            // newPhotosSection.style.display = 'block';
        }
        
        // Create a list of restored photos for display
        if (photoList) {
            restoredPhotos.forEach((photoPath, index) => {
                const photoItem = document.createElement('div');
                photoItem.className = 'photo-list-item restored-photo';
                photoItem.dataset.photoPath = photoPath;
                
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
            });
        }
        
        // Create hidden inputs for restored photos
        const form = document.getElementById('home-form');
        if (form) {
            restoredPhotos.forEach((photoPath, index) => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'restored_photos[]';
                hiddenInput.value = photoPath;
                hiddenInput.className = 'restored-photo-input';
                form.appendChild(hiddenInput);
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
});
</script>
@endif

@endpush
