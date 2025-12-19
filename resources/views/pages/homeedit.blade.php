@extends('layouts.master')

@section('title', __('Edit Home Data Entry'))

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/forminput-table.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/home-common.css') }}" rel="stylesheet">
    <style>
        .row-deleted {
            opacity: .45;
            filter: grayscale(0.6);
        }
    </style>
@endpush

@section('content')
    <form id="home-form" action="{{ route('home.update', $home) }}" method="POST" data-preview-action="{{ route('home.preview') }}" novalidate>
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

                                            <option value="{{ __('Pi Kaew company') }}"
                                                {{ old('trooper', $home->trooper) === __('Pi Kaew company') ? 'selected' : '' }}>
                                                {{ __('Pi Kaew company') }}
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
                    <input type="number" step="0.01" class="form-control js-amount"
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
                    <input type="number" step="0.01" class="form-control js-mc"
                           name="details[{{ $i }}][mc_price]"
                           value="{{ $detail->mc_price }}"
                           data-error-number="{{ __('Please enter number only') }}">
                </div>
            </div>

            <!-- Labor Price / Unit -->
            <div class="col-6 col-md-2 field-col">
                <label class="form-label">{{ __('Labor Price') }} <span class="star">*</span></label>
                <div class="input-group input-42">
                    <input type="number" step="0.01" class="form-control js-lc"
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
                    <input type="number" step="0.01"
                           class="form-control readonly-input js-mat-total"
                           value="{{ number_format((float) $detail->material_total, 2, '.', '') }}"
                           readonly>
                </div>
            </div>

            <!-- Labor Total -->
            <div class="col-12 col-md-2 field-col">
                <label class="form-label">{{ __('Labor Total') }}</label>
                <div class="input-group input-42">
                    <input type="number" step="0.01"
                           class="form-control readonly-input js-lab-total"
                           value="{{ number_format((float) $detail->labor_total, 2, '.', '') }}"
                           readonly>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="col-12 col-md-2 field-col">
                <label class="form-label">{{ __('Grand Total') }}</label>
                <div class="input-group input-42">
                    <input type="number" step="0.01"
                           class="form-control readonly-input js-grand-total"
                           value="{{ number_format((float) $detail->grand_total, 2, '.', '') }}"
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

                {{-- ===== Actions + Summary (same block as create) ===== --}}
                <div class="col-lg-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <div class="row">
                                <div class="col-md-8 col-5">
                                    <div class="card-body left-controls">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" id="add-row-btn-edit">
                                                + {{ __('Add Row') }}
                                            </button>
                                        </div>
                                        <div class="form-group btn-generate d-flex flex-wrap gap-2">
                                            <a href="{{ route('home') }}" class="btn btn-secondary">
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
                                <div class="col-4 col-sm-4 col-md-3 col-lg-4 ms-md-auto summary-col">
                                    <div class="text-end" style="min-width:200px;margin-left:auto">
                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong>{{ __('Total') }}:</strong>
                                            <span class="ms-2" id="miscDisplay">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                            <strong class="text-start">
                                                {{-- Desktop / big screen: one line --}}
                                                <span class="d-none d-md-inline">
                                                    {{ __('Operating + Profit (15%)') }}
                                                </span>

                                                {{-- Mobile / small screen: two lines --}}
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

                                            {{-- Mobile / small screen: two lines --}}
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

                                        {{-- Hidden senders for summary if you need them --}}
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

            </div>{{-- /row --}}
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
                            <input type="number" step="0.01" class="form-control js-amount"
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
                            <input type="number" step="0.01" class="form-control js-mc" name="details[__INDEX__][mc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-6 col-md-2 field-col">
                        <label class="form-label">{{ __('Material Total') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control readonly-input js-mat-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                    <div class="col-md-6"></div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Labor Price') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control js-lc" name="details[__INDEX__][lc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Labor Total') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control readonly-input js-lab-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 field-col">
                        <label class="form-label">{{ __('Grand Total') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control readonly-input js-grand-total" value="0.00"
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
    <script src="{{ asset('assets/plugins/validations/homeValidation.js') }}"></script>
    <script src="{{ asset('assets/plugins/home/home-form.js') }}"></script>
@endpush
