@extends('layouts.master')

@section('title', __('Edit Condo Excel Form'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/condo-forminput-table.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
  <form action="{{ route('condo.update', $condo) }}" method="POST" id="condo-form">
    @csrf
    @method('PUT')

    <div class="xp-contentbar">

      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>{{ __('Please fix the errors below.') }}</strong>
        </div>
      @endif

      <div class="row">
        <!-- Customer -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Customer Name') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="customer_name"
                           id="inputText"
                           placeholder="{{ __('Enter Customer name') }}"
                           value="{{ old('customer_name', $condo->customer_name) }}">
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

        <!-- Address -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Address') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="address"
                           id="inputEmail"
                           placeholder="{{ __('Enter Address') }}"
                           value="{{ old('address', $condo->address) }}">
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

        <!-- Job Name -->
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Job Name') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="job_name"
                           id="inputPassword"
                           placeholder="{{ __('Job Name') }}"
                           value="{{ old('job_name', $condo->job_name) }}">
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

        <!-- Quotation Number -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Quotation Number') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="quotation_number"
                           id="inputNumber"
                           placeholder="{{ __('Enter QN') }}"
                           value="{{ old('quotation_number', $condo->quotation_number) }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                  @error('quotation_number')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Date -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Date') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <input type="date"
                         class="form-control"
                         name="quotation_date"
                         id="inputDate"
                         value="{{ old('quotation_date', optional($condo->quotation_date)->format('Y-m-d')) }}">
                  @error('quotation_date')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Term -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Payment Term') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="payment_term"
                           id="inputSearch"
                           placeholder="{{ __('Enter PT') }}"
                           value="{{ old('payment_term', $condo->payment_term) }}">
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

        <!-- Credits -->
        <div class="col-lg-3 col-md-3 col-6">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Credits') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="credits"
                           id="inputUrl"
                           placeholder="{{ __('Enter Credits') }}"
                           value="{{ old('credits', $condo->credits) }}">
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

        {{-- =========================
             LINE ITEMS (prefilled)
        ========================== --}}
        @php
          $oldItems = old('items');
          $items = $oldItems ?? $condo->details->map(function($d) {
              return [
                'no' => $d->no,
                'details' => $d->details,
                'amount' => $d->amount,
                'unit' => $d->unit,
                'material_cost' => $d->material_cost,
                'labor_cost' => $d->labor_cost,
                'price_per_unit_total' => $d->price_per_unit_total,
              ];
          })->values()->toArray();

          if (empty($items)) {
            $items = [
              ['no' => 1, 'details' => null, 'amount' => null, 'unit' => null, 'material_cost' => null, 'labor_cost' => null, 'price_per_unit_total' => null]
            ];
          }
        @endphp

        <div id="rows-container" class="w-100">
          @foreach ($items as $i => $row)
          <div class="col-lg-12">
            <div class="card m-b-2 item-row">
              <div class="card-header bg-white">
                <div class="row">
                  <div class="col-lg-1 col-md-1 col-2">
                    <h5 class="card-title text-black">{{ __('No') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text" class="form-control serial" value="{{ $row['no'] ?? $i + 1 }}" readonly>
                        <input type="hidden" name="items[{{ $i }}][no]" value="{{ $row['no'] ?? $i + 1 }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-5 col-md-4 col-10">
                    <h5 class="card-title text-black">{{ __('Details') }}</h5>
                    <div class="card-body">
                      <div class="input-group">
                        <input type="text"
                               class="form-control detail"
                               name="items[{{ $i }}][details]"
                               placeholder="{{ __('Details') }}"
                               value="{{ $row['details'] }}">
                        <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                          <i class="fas fa-microphone"></i>
                        </button>
                      </div>
                      @error("items.$i.details")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                  </div>

                  <div class="col-lg-1 col-md-2 col-4">
                    <h5 class="card-title text-black">{{ __('Amount') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text"
                               class="form-control amount"
                               name="items[{{ $i }}][amount]"
                               placeholder=".00"
                               value="{{ $row['amount'] }}">
                      </div>
                      @error("items.$i.amount")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                  </div>

                  <div class="col-md-1 col-4">
                    <h5 class="card-title text-black">{{ __('Unit') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text"
                               class="form-control units"
                               name="items[{{ $i }}][unit]"
                               placeholder="{{ __('Units') }}"
                               value="{{ $row['unit'] }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-2 col-4">
                    <h5 class="card-title text-black">{{ __('Material Cost') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text"
                               class="form-control material"
                               name="items[{{ $i }}][material_cost]"
                               placeholder=".00"
                               value="{{ $row['material_cost'] }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-2 col-4">
                    <h5 class="card-title text-black">{{ __('Labor Cost') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text"
                               class="form-control labor"
                               name="items[{{ $i }}][labor_cost]"
                               placeholder=".00"
                               value="{{ $row['labor_cost'] }}">
                      </div>
                    </div>
                  </div>

                  <div class="d-none d-md-block col-md-8"></div>

                  <div class="col-md-2 col-4">
                    <h5 class="card-title text-black">{{ __('Price Amount') }}</h5>
                    <div class="card-body">
                      <div class="form-group">
                        <input type="text"
                               class="form-control price-per-unit"
                               name="items[{{ $i }}][price_per_unit_total]"
                               placeholder=".00"
                               value="{{ $row['price_per_unit_total'] }}">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-2 col-4">
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

                </div> <!-- row -->
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Footer actions + totals -->
        <div class="col-lg-12 ">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <div class="row">
                <div class="col-md-8 col-5">
                  <div class="card-body">
                    <div class="form-group">
                      <button type="button" class="btn btn-primary" id="add-row-btn">{{ __('+ Add Row') }}</button>
                    </div>

                    <div class="form-group d-flex flex-column flex-sm-row pt-1 pt-md-5">
                      <a href="{{ route('condo') }}" class="btn btn-secondary mb-2 mb-sm-0">
                        {{ __('Cancel') }}
                      </a>
                      <button type="submit" class="btn btn-success ml-sm-2" id="generate-btn">
                        {{ __('Update') }}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="col-md3 col-4">
                  <div class="text-end" style="min-width: 200px; margin-left: auto">
                    <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
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

              </div> <!-- row -->
            </div>
          </div>
        </div>

      </div> <!-- /row -->

    </div>

    {{-- Hidden template for adding new rows --}}
    <template id="row-template">
      <div class="col-lg-12">
        <div class="card m-b-2 item-row">
          <div class="card-header bg-white">
            <div class="row">

              <div class="col-lg-1 col-md-1 col-2">
                <h5 class="card-title text-black">{{ __('No') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control serial" value="__SERIAL__" readonly>
                    <input type="hidden" name="items[__INDEX__][no]" value="__SERIAL__">
                  </div>
                </div>
              </div>

              <div class="col-lg-5 col-md-4 col-10">
                <h5 class="card-title text-black">{{ __('Details') }}</h5>
                <div class="card-body">
                  <div class="input-group">
                    <input type="text" class="form-control detail" name="items[__INDEX__][details]" placeholder="{{ __('Details') }}">
                    <button type="button" class="btn btn-outline-secondary mic-btn" title="{{ __('Speak') }}" onclick="startDictation(this)">
                      <i class="fas fa-microphone"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="col-lg-1 col-md-2 col-4">
                <h5 class="card-title text-black">{{ __('Amount') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control amount" name="items[__INDEX__][amount]" placeholder=".00">
                  </div>
                </div>
              </div>

              <div class="col-md-1 col-4">
                <h5 class="card-title text-black">{{ __('Unit') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control units" name="items[__INDEX__][unit]" placeholder="{{ __('Units') }}">
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-4">
                <h5 class="card-title text-black">{{ __('Material Cost') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control material" name="items[__INDEX__][material_cost]" placeholder=".00">
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-4">
                <h5 class="card-title text-black">{{ __('Labor Cost') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control labor" name="items[__INDEX__][labor_cost]" placeholder=".00">
                  </div>
                </div>
              </div>

              <div class="d-none d-md-block col-md-8"></div>

              <div class="col-md-2 col-4">
                <h5 class="card-title text-black">{{ __('Price Amount') }}</h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control price-per-unit" name="items[__INDEX__][price_per_unit_total]" placeholder=".00">
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-4">
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
<script src="{{ asset('assets/plugins/condo/condo-form.js') }}"></script>
<script>
  // Ensure totals are recalculated on page load with prefilled values
  document.addEventListener('DOMContentLoaded', () => {
    if (window.CondoForm && typeof CondoForm.recalcAll === 'function') {
      CondoForm.recalcAll();
    } else {
      // fallback: trigger input events to recalc if your JS listens to 'input'
      document.querySelectorAll('#rows-container input').forEach(el => {
        el.dispatchEvent(new Event('input', { bubbles: true }));
      });
    }
  });
</script>
@endpush
