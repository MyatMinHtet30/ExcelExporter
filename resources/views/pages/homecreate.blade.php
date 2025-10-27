@extends('layouts.master')

@section('title', __('New Home Data Entry'))

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/forminput-table.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* ---------- General polish ---------- */
        .item-row {
            position: relative
        }

        .item-row .card-header {
            padding: 1rem 1rem .75rem
        }

        .item-row .card-body {
            padding: .25rem 1rem 1rem
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: .35rem
        }

        .input-group.input-42>.form-control,
        .input-group.input-42>.btn {
            height: 42px
        }

        .readonly-input[readonly] {
            background: #f1f3f5
        }

        .field-col {
            display: flex;
            flex-direction: column
        }

        .field-col .input-group,
        .field-col .form-control {
            width: 100%
        }

        .g-compact {
            row-gap: .75rem
        }

        .remove-row {
            position: static;
            line-height: 1;
            margin-top: .5rem;
            border-radius: 4px;
            padding: .25rem .75rem;
        }

        /* Mobile tweaks for 2nd row fields */
        @media (max-width: 576px) {
            .item-row .card-header {
                padding: .75rem .75rem
            }

            .g-compact {
                row-gap: .5rem
            }

            .input-group.input-42>.form-control,
            .input-group.input-42>.btn {
                height: 40px
            }

            .mic-btn {
                min-width: 40px
            }

            .fields-line-2>.field-col {
                flex: 0 0 50%;
                max-width: 50%
            }

            .readonly-input {
                height: 40px
            }
        }

        @media (min-width: 577px) {
            .item-row .remove-mobile {
                display: none
            }
        }

        /* --- Keep Generate at the bottom on the left --- */
        .left-controls {
            display: flex;
            flex-direction: column;
            height: 100%
        }

        .left-controls .btn-generate {
            margin-top: auto
        }

        /* ===== iPhone SE / narrow phones: keep 2-col layout, widen summary, slim buttons ===== */
        @media (max-width: 420px) {

            /* make left (buttons) narrower, right (summary) wider */
            .card-header .row>.col-md-8.col-5 {
                flex: 0 0 40% !important;
                max-width: 40% !important;
                width: 40% !important;
            }

            .card-header .row>.col-md-3.col-4 {
                flex: 0 0 60% !important;
                max-width: 60% !important;
                width: 60% !important;
            }

            /* let summary panel fit inside its col-4 */
            .card-header .row>.col-md-3.col-4 .text-end {
                min-width: 0 !important;
                width: 100% !important;
                margin-left: 0 !important;
            }

            /* slim ONLY Add Row & Generate (not mic buttons) */
            #add-row-btn,
            #generate-btn {
                padding: .35rem .5rem;
                font-size: .85rem;
                line-height: 1.2;
            }

            .left-controls .form-group {
                margin-bottom: .5rem;
            }
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('home.store') }}" method="POST">
        @csrf
        <div class="xp-contentbar">
            <div class="row">

                <!-- Top info cards -->
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Project Name') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="project_name" id="project_name"
                                            placeholder="{{ __('Enter project name') }}" value="{{ old('project_name') }}" required>
                                        @error('project_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
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
                            <h5 class="card-title text-black">{{ __('Dear') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="dear" id="dear" placeholder="{{ __('Enter recipient name') }}"
                                            value="{{ old('dear') }}" required>
                                        @error('dear') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
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
                            <h5 class="card-title text-black">{{ __('List Name') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="list_name" id="list_name" placeholder="{{ __('Enter list name') }}"
                                            value="{{ old('list_name') }}" required>
                                        @error('list_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('House No') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="house_no" id="house_no" placeholder="{{ __('Enter house no.') }}"
                                            value="{{ old('house_no') }}" required>
                                        @error('house_no') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Trooper') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="trooper" id="trooper" placeholder="{{ __('Enter trooper') }}"
                                            value="{{ old('trooper') }}" required>
                                        @error('trooper') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- Dynamic item rows ---------- -->
                <div id="rows-container" class="col-12">
                    <div class="card m-b-2 item-row">
                        <div class="card-header bg-white">

                            <!-- Line 1 -->
                            <div class="row g-3 g-compact align-items-end">
                                <div class="col-3 col-sm-2 col-md-1 field-col">
                                    <label class="form-label">{{ __('No') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" class="form-control readonly-input serial" name="details[0][no]" value="1"
                                            min="1" readonly>
                                    </div>
                                </div>

                                <!-- OPTIONAL: category -->
                                <div class="col-12 col-sm-4 col-md-3 field-col">
                                    <label class="form-label">{{ __('Category') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="details[0][category_name]"
                                            placeholder="{{ __('Category A / B / ...') }}">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-8 col-md-4 field-col">
                                    <label class="form-label">{{ __('Item Name') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="details[0][item_name]"
                                            placeholder="{{ __('Enter item name') }}" required>
                                        <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)"
                                            title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2 field-col">
                                    <label class="form-label">{{ __('Amount') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control" name="details[0][amount]" placeholder=".00"
                                            required>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2 field-col">
                                    <label class="form-label">{{ __('Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="details[0][unit]" placeholder="{{ __('Unit') }}"
                                            required>
                                        <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)"
                                            title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Line 2 -->
                            <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                                <div class="col-12 col-md field-col">
                                    <label class="form-label">{{ __('Material Price / Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control" name="details[0][mc_price]" placeholder=".00"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="form-label">{{ __('Material Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control readonly-input js-mat-total" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="form-label">{{ __('Labor Price / Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control" name="details[0][lc_price]" placeholder=".00"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="form-label">{{ __('Labor Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control readonly-input js-lab-total" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="form-label">{{ __('Grand Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="number" step="0.01" class="form-control readonly-input js-grand-total" placeholder="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row pt-2">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Actions + Summary (condo layout) ===== -->
                <div class="col-lg-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <div class="row">
                                <!-- Left: buttons -->
                                <div class="col-md-8 col-5">
                                    <div class="card-body left-controls">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" id="add-row-btn">+
                                                {{ __('Add Row') }}</button>
                                        </div>
                                        <div class="form-group btn-generate">
                                            <button type="submit" class="btn btn-success"
                                                id="generate-btn">{{ __('Generate') }}</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: compact totals -->
                                <div class="col-md-3 col-4">
                                    <div class="text-end" style="min-width: 200px; margin-left: auto">

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('Total') }}:</strong>
                                            <span class="ms-2" id="miscDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-block">{{ __('Operating +') }}</span>
                                                <span class="d-block">{{ __('Profit (15%)') }}</span>
                                            </strong>
                                            <span class="ms-2" id="operatingDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-block">{{ __('Category A,B') }}</span>
                                                <span class="d-block">{{ __('Total') }}</span>
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
                <!-- ===== /Actions + Summary ===== -->

            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/home/home-form.js') }}"></script>
@endpush
