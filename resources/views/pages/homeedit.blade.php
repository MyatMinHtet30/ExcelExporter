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
    <form action="{{ route('home.update', $home) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="xp-contentbar">
            <div class="row">

                {{-- Top info cards (same as create) --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Project Name') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="project_name" id="project_name"
                                            value="{{ old('project_name', $home->project_name) }}" required>
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

                {{-- Dear --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('Dear') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="dear" id="dear"
                                            value="{{ old('dear', $home->dear) }}" required>
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

                {{-- List Name --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('List Name') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="list_name" id="list_name"
                                            value="{{ old('list_name', $home->list_name) }}" required>
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

                {{-- House No + Trooper --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card m-b-20">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-black">{{ __('House No') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="house_no" id="house_no"
                                            value="{{ old('house_no', $home->house_no) }}" required>
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
                            <h5 class="card-title text-black">{{ __('Trooper') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="text" class="form-control" name="trooper" id="trooper"
                                            value="{{ old('trooper', $home->trooper) }}" required>
                                        @error('trooper')<small
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
                            <h5 class="card-title text-black">{{ __('Date') }}</h5>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group input-42">
                                        <input type="date" class="form-control" name="date" id="date"
                                            value="{{ old('date', $home->date?->format('Y-m-d')) }}" required>
                                        @error('date') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
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
                                {{-- line 1 --}}
                                <div class="row g-3 g-compact align-items-end">
                                    <input type="hidden" name="details[{{ $i }}][id]" value="{{ $detail->id }}">
                                    <input type="hidden" name="details[{{ $i }}][_delete]" value="0" class="js-delete-flag">

                                    <div class="col-3 col-sm-2 col-md-1 field-col">
                                        <label class="form-label">{{ __('No') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" class="form-control readonly-input serial"
                                                name="details[{{ $i }}][no]" value="{{ $detail->no }}" min="1" readonly>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6 col-md-6 field-col">
                                        <label class="form-label">{{ __('Category') }}</label>
                                        <div class="input-group input-42">
                                            <input type="text" class="form-control" name="details[{{ $i }}][category_name]"
                                                value="{{ $detail->category_name }}">
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
                                                name="details[{{ $i }}][amount]" value="{{ $detail->amount }}">
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-2 field-col">
                                        <label class="form-label">{{ __('Unit') }}</label>
                                        <div class="input-group input-42">
                                            <input type="text" class="form-control" name="details[{{ $i }}][unit]"
                                                value="{{ $detail->unit }}">
                                            <button type="button" class="btn btn-outline-secondary mic-btn"
                                                onclick="startDictation(this)">
                                                <i class="fas fa-microphone"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- line 2 --}}
                                <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                                    <div class="col-12 col-md field-col">
                                        <label class="form-label">{{ __('Material Price / Unit') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" step="0.01" class="form-control js-mc"
                                                name="details[{{ $i }}][mc_price]" value="{{ $detail->mc_price }}">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md field-col">
                                        <label class="form-label">{{ __('Material Total') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" step="0.01" class="form-control readonly-input js-mat-total"
                                                value="{{ number_format((float) $detail->material_total, 2, '.', '') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md field-col">
                                        <label class="form-label">{{ __('Labor Price / Unit') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" step="0.01" class="form-control js-lc"
                                                name="details[{{ $i }}][lc_price]" value="{{ $detail->lc_price }}">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md field-col">
                                        <label class="form-label">{{ __('Labor Total') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" step="0.01" class="form-control readonly-input js-lab-total"
                                                value="{{ number_format((float) $detail->labor_total, 2, '.', '') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md field-col">
                                        <label class="form-label">{{ __('Grand Total') }}</label>
                                        <div class="input-group input-42">
                                            <input type="number" step="0.01" class="form-control readonly-input js-grand-total"
                                                value="{{ number_format((float) $detail->grand_total, 2, '.', '') }}" readonly>
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
                                            <button type="button" class="btn btn-primary" id="add-row-btn">+
                                                {{ __('Add Row') }}</button>
                                        </div>
                                        <div class="form-group btn-generate d-flex flex-wrap gap-2">
                                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                                {{ __('Cancel') }}
                                            </a>
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
                    <div class="col-12 col-sm-6 col-md-6 field-col">
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
                            <input type="text" class="form-control" name="details[__INDEX__][unit]">
                            <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
                    <div class="col-12 col-md field-col">
                        <label class="form-label">{{ __('Material Price / Unit') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control js-mc" name="details[__INDEX__][mc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-12 col-md field-col">
                        <label class="form-label">{{ __('Material Total') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control readonly-input js-mat-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md field-col">
                        <label class="form-label">{{ __('Labor Price / Unit') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control js-lc" name="details[__INDEX__][lc_price]"
                                placeholder=".00">
                        </div>
                    </div>
                    <div class="col-12 col-md field-col">
                        <label class="form-label">{{ __('Labor Total') }}</label>
                        <div class="input-group input-42">
                            <input type="number" step="0.01" class="form-control readonly-input js-lab-total" value="0.00"
                                readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md field-col">
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
    <script src="{{ asset('assets/plugins/home/home-form.js') }}"></script>
    <script>
        (function () {
            // Reuse your existing calc logic; just add helpers for add/remove

            let nextIndex = {{ count($home->details) }};

            document.getElementById('add-row-btn')?.addEventListener('click', function () {
                const tpl = document.getElementById('row-template').innerHTML;
                const ser = nextIndex + 1; // serial display
                const html = tpl.replaceAll('__INDEX__', nextIndex).replaceAll('__SER__', ser);
                const wrap = document.createElement('div');
                wrap.innerHTML = html;
                const row = wrap.firstElementChild;
                document.getElementById('rows-container').appendChild(row);
                nextIndex++;
            });

            window.markRowDeleted = function (btn) {
                const card = btn.closest('.item-row');
                const del = card.querySelector('.js-delete-flag');
                if (!del) return;
                // toggle delete
                const isDeleted = del.value === '1';
                del.value = isDeleted ? '0' : '1';
                card.classList.toggle('row-deleted', !isDeleted);
            };

            if (typeof window.recomputeSummary === 'function') {
                window.recomputeSummary();
            }
        })();
    </script>
@endpush
