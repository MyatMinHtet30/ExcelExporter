@extends('layouts.master')

@section('title', __('Generate Condo Excel Form'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/condo-forminput-table.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
  <form action="{{ route('condo.store') }}" method="POST" id="condo-form" novalidate>
    @csrf
    <div class="xp-contentbar">

      {{-- Top-level validation summary (optional) --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>{{ __('Please fix the errors below.') }}</strong>
        </div>
      @endif

      <!-- Start XP Row -->
      <div class="row">

        <!-- Start XP Col -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Customer Name') }}*</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: customer_name -->
                    <input type="text" class="form-control" name="customer_name" id="inputText" placeholder="{{ __('Enter Customer name') }}" value="{{ old('customer_name') }}" required
                    data-error-required="{{ __('Customer name is required') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('customer_name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        <!-- Start XP Col -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Address') }}*</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: address -->
                    <input type="text" class="form-control" name="address" id="inputEmail" placeholder="{{ __('Enter Address') }}" value="{{ old('address') }}" required 
                    data-error-required="{{ __('Address is required') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('address')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        <!-- Start XP Col -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Job Name') }}*</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: job_name -->
                    <input type="text" class="form-control" name="job_name" id="inputPassword" placeholder="{{ __('Job Name') }}" value="{{ old('job_name') }}" required 
                    data-error-required="{{ __('Job name is required') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('job_name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        <!-- Start XP Col -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Quotation Number') }}*</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: quotation_number -->
                    <input type="text" class="form-control" name="quotation_number" id="inputNumber" value="{{ old('quotation_number', $nextQuotationNumber ?? '') }}" readonly required
                    data-error-required="{{ __('Quotation number is required') }}">
                    <!-- <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button> -->
                  </div>
                  @error('quotation_number')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        <!-- Start XP Col -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Payment Term') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: payment_term -->
                    <input type="text" class="form-control" name="payment_term" id="inputSearch" placeholder="{{ __('Enter PT') }}" value="{{ old('payment_term') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('payment_term')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        <!-- Start XP Col -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Credits') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: credits -->
                    <input type="text" class="form-control" name="credits" id="inputUrl" placeholder="{{ __('Enter Credits') }}" value="{{ old('credits') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('credits')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End XP Col -->

        {{-- =========================
             LINE ITEMS (rows)
        ========================== --}}
        @php
          // Repopulate rows if validation failed; otherwise render one blank row
          $oldItems = old('items', [
            ['no' => 1, 'details' => null, 'amount' => null, 'unit' => null, 'material_cost' => null, 'labor_cost' => null, 'price_per_unit_total' => null]
          ]);
        @endphp

        <div id="rows-container" class="w-100">
          @foreach ($oldItems as $i => $row)
          <div class="col-lg-12">
            <div class="card m-b-2 item-row">
              <div class="card-header bg-white">
                <div class="row">

                  <div class="col-3 col-sm-2 col-md-1 field-col">
                    <h5 class="card-title text-black">{{ __('No') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control serial" value="{{ $i + 1 }}" readonly>
                        <input type="hidden" name="items[{{ $i }}][no]" value="{{ $i + 1 }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-12 col-sm-6 col-md-3 field-col mb-3">
                    <h5 class="card-title text-black">{{ __('Details') }}*</h5>
                    <div class="card-body">
                      <div class="input-group">
                        <input type="text" class="form-control detail" name="items[{{ $i }}][details]" placeholder="{{ __('Details') }}" value="{{ $row['details'] }}" required 
                        data-error-required="{{ __('Details is required') }}">
                        <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                          <i class="fas fa-microphone"></i>
                        </button>
                      </div>
                      @error("items.$i.details")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                  </div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Amount') }}*</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control amount" name="items[{{ $i }}][amount]" placeholder=".00" value="{{ $row['amount'] }}" required
                        data-error-required="{{ __('Amount is required') }}"
                        data-error-number="{{ __('Please enter number only') }}">
                      </div>
                      @error("items.$i.amount")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                  </div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Unit') }}*</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control units" name="items[{{ $i }}][unit]" placeholder="{{ __('Units') }}" value="{{ $row['unit'] }}" required 
                        data-error-required="{{ __('Unit is required') }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Material Cost') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control material" name="items[{{ $i }}][material_cost]" placeholder=".00" value="{{ $row['material_cost'] }}" required 
                        data-error-number="{{ __('Please enter number only') }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Labor Cost') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control labor" name="items[{{ $i }}][labor_cost]" placeholder=".00" value="{{ $row['labor_cost'] }}"
                        data-error-number="{{ __('Please enter number only') }}">
                      </div>
                    </div>
                  </div>

                  <div class="d-none d-md-block col-md-8"></div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Price Amount') }}*</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control price-per-unit" name="items[{{ $i }}][price_per_unit_total]" placeholder=".00" value="{{ $row['price_per_unit_total'] }}" required
                        data-error-required="{{ __('Price per unit is required') }}"
                        data-error-number="{{ __('Please enter number only') }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-6 col-md-2 field-col">
                    <h5 class="card-title text-black">{{ __('Subtotal') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <!-- UI-only; no name -->
                        <input type="text" class="form-control subtotal" placeholder=".00" readonly>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="d-flex justify-content-end">
                      <button type="button" class="btn btn-sm btn-danger remove-row">&times;</button>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="col-lg-12 ">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <div class="row">

                <div class="col-md-8 col-5">
                  <div class="card-body">
                    <div class="form-group">
                      <button type="button" class="btn btn-primary pl-2 pr-3 pt-md-2" id="add-row-btn"><span aria-hidden="true">+</span> {{ __('Add Row') }}</button>
                    </div>

                   <div class="form-group d-flex flex-column flex-sm-row pt-1 pt-md-5">
                    {{-- Cancel back to condo list --}}
                    <a href="{{ route('condo') }}" class="btn btn-secondary mb-2 mb-sm-0">
                      {{ __('Cancel') }}
                    </a>

                    <button type="submit" class="btn btn-primary mb-2 mb-sm-0 ml-sm-2" formaction="{{ route('condo.preview') }}">
                      {{ __('Preview') }}
                    </button>

                    {{-- Generate submit --}}
                    <button type="submit" class="btn btn-success ml-sm-2" id="generate-btn">
                      {{ __('Generate') }}
                    </button>
                  </div>
                  </div>
                </div>

                <div class="col-md3 col-4">
                  <div class="text-end" style="min-width: 200px; margin-left: auto">
                    <div class="d-flex justify-content-between border p-2 mb-2 bg-light ">
                      <strong>{{ __('Total') }}:</strong>
                      <span class="ms-2" id="totalDisplay">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                      <strong>{{ __('VAT 7%') }}:</strong>
                      <span class="ms-2" id="taxDisplay">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border p-2 bg-light">
                      <strong>{{ __('Total Price') }}:</strong>
                      <span class="ms-2" id="totalPriceDisplay">0.00</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- end row -->

    </div>

    {{-- Hidden template for new rows (JS will replace __INDEX__) --}}
    <template id="row-template">
      <div class="col-lg-12">
        <div class="card m-b-2 item-row">
          <div class="card-header bg-white">
            <div class="row">

              <div class="col-3 col-sm-2 col-md-1 field-col">
                <h5 class="card-title text-black">{{ __('No') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control serial" value="__SERIAL__" readonly>
                    <input type="hidden" name="items[__INDEX__][no]" value="__SERIAL__">
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3 field-col">
                <h5 class="card-title text-black">{{ __('Details') }}*</h5>
                <div class="card-body">
                  <div class="input-group">
                    <input type="text" class="form-control detail" name="items[__INDEX__][details]" placeholder="{{ __('Details') }}" required
                     data-error-required="{{ __('Details is required') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Amount') }}*</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control amount" name="items[__INDEX__][amount]" placeholder=".00" required 
                    data-error-required="{{ __('Amount is required') }}"
                    data-error-number="{{ __('Please enter number only') }}">
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Unit') }}*</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control units" name="items[__INDEX__][unit]" placeholder="{{ __('Units') }}" required
                    data-error-required="{{ __('Unit is required') }}">
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Material Cost') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control material" name="items[__INDEX__][material_cost]" placeholder=".00" required
                    data-error-number="{{ __('Please enter number only') }}">
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Labor Cost') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control labor" name="items[__INDEX__][labor_cost]" placeholder=".00" required 
                    data-error-number="{{ __('Please enter number only') }}">
                  </div>
                </div>
              </div>

              <div class="d-none d-md-block col-md-8"></div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Price Amount') }}*</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control price-per-unit" name="items[__INDEX__][price_per_unit_total]" placeholder=".00" required
                    data-error-required="{{ __('Price per unit is required') }}"
                    data-error-number="{{ __('Please enter number only') }}">
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Subtotal') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control subtotal" placeholder=".00" readonly>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-sm btn-danger remove-row">&times;</button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </template>

  </form>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/validations/condoValidation.js') }}"></script>
<script src="{{ asset('assets/plugins/condo/condo-form.js') }}"></script>
@endpush
