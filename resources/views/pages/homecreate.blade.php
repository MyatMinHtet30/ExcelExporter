@extends('layouts.master')

@section('title', __('Generate Home Excel Form'))

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/home-forminput-table.css') }}   " rel="stylesheet" type="text/css" />
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
                                            placeholder="{{ __('Enter project name') }}" required>
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
                                        <input type="text" class="form-control" name="dear" id="dear"
                                            placeholder="{{ __('Enter recipient name') }}" required>
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
                            <h5 class="card-title text-black">{{ __('Trooper') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="trooper" id="trooper"
                                            placeholder="{{ __('Enter trooper') }}" required>
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

                <div class="col-lg-4 col-md-4 col-6">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Date') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="inputDate" id="inputDate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-6">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('House No') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="house_no" id="house_no"
                                            placeholder="{{ __('Enter house no.') }}" required>
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
                                        <input type="text" class="form-control" name="list_name" id="list_name"
                                            placeholder="{{ __('Enter list name') }}" required>
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
                                    <label class="card-title text-black">{{ __('No') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control readonly-input serial" value="1" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-10 col-md-6 field-col">
                                    <label class="card-title text-black">{{ __('Category / Details') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="items[0][details]"
                                            placeholder="{{ __('Enter item name') }}" required>
                                        <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2 field-col">
                                    <label class="card-title text-black">{{ __('Amount') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" class="form-control number-input" name="items[0][amount]"
                                            placeholder=".00" required>
                                    </div>
                                </div>

                                <div class="col-6 col-md-3 field-col">
                                    <label class="card-title text-black">{{ __('Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="items[0][unit]"
                                            placeholder="{{ __('Unit') }}" required>
                                        <!-- <button type="button" class="btn btn-outline-secondary mic-btn"
                                            onclick="startDictation(this)" title="{{ __('Speak') }}">
                                            <i class="fas fa-microphone"></i>
                                        </button> -->
                                    </div>
                                </div>
                            </div>

                            <!-- Line 2 -->
                            <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                                <div class="col-12 col-md field-col">
                                    <label class="card-title text-black">{{ __('Material Price / Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" class="form-control number-input"
                                            name="items[0][material_unit_price]" placeholder=".00" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="card-title text-black">{{ __('Material Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" name="items[0][material_total]"
                                            class="form-control readonly-input number-input" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="card-title text-black">{{ __('Labor Price / Unit') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" class="form-control number-input"
                                            name="items[0][labor_unit_price]" placeholder=".00" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="card-title text-black">{{ __('Labor Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" name="items[0][labor_total]"
                                            class="form-control readonly-input number-input" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md field-col">
                                    <label class="card-title text-black">{{ __('Grand Total') }}</label>
                                    <div class="input-group input-42">
                                        <input type="text" step="0.01" name="items[0][grand_total]"
                                            class="form-control readonly-input number-input" placeholder="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row pt-2">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled>&times;</button>
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
                                <div class="col-md3 col-4">
                                    <div class="text-end" style="min-width: 200px; margin-left: auto">

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('Total') }}:</strong>
                                            <span class="ms-2" id="miscDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-block">{{ __('Operating Profit 15%') }}:</span>
                                            </strong>
                                            <span class="ms-2" id="operatingDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                <span class="d-block">{{ __('Category A,B Total') }}:</span>
                                            </strong>
                                            <span class="ms-2" id="abDisplay">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('VAT 7%') }}</strong>
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
