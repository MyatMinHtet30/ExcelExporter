@extends('layouts.master')

@section('title', __('Generate Condo Excel Form'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/condo-forminput-table.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/home-common.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/photo-upload.css') }}" rel="stylesheet">
@endpush

@section('content')
  @php
    // Helper function to get restored value or old value
    $getValue = function($field, $default = '') use ($restoredData) {
        if (isset($restoredData) && isset($restoredData[$field])) {
            return $restoredData[$field];
        }
        return old($field, $default);
    };
  @endphp
  
  <form action="{{ route('condo.store') }}" method="POST" id="condo-form" novalidate enctype="multipart/form-data">
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
              <h5 class="card-title text-black">{{ __('Customer Name') }} <span class="star" >*</span></h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: customer_name -->
                    <input type="text" class="form-control" name="customer_name" id="inputText" placeholder="{{ __('Enter Customer name') }}" value="{{ $getValue('customer_name') }}" required
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
              <h5 class="card-title text-black">{{ __('Address') }} <span class="star" >*</span></h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: address -->
                    <input type="text" class="form-control" name="address" id="inputEmail" placeholder="{{ __('Enter Address') }}" value="{{ $getValue('address') }}" required 
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
              <h5 class="card-title text-black">{{ __('Job Name') }}<span class="star" >*</span></h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: job_name -->
                    <input type="text" class="form-control" name="job_name" id="inputPassword" placeholder="{{ __('Job Name') }}" value="{{ $getValue('job_name') }}" required 
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
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Quotation Number') }}<span class="star" >*</span></h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: quotation_number -->
                    <input type="text" class="form-control" name="quotation_number" id="inputNumber" value="{{ $getValue('quotation_number', $nextQuotationNumber ?? '') }}" readonly required
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
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Payment Term') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: payment_term -->
                    <input type="text" class="form-control" name="payment_term" id="inputSearch" placeholder="{{ __('Enter PT') }}" value="{{ $getValue('payment_term') }}">
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
        <div class="col-lg-4 col-md-4 col-12">
          <div class="card m-b-20">
            <div class="card-header bg-white">
              <h5 class="card-title text-black">{{ __('Credits') }}</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="input-group">
                    <!-- bind: credits -->
                    <input type="text" class="form-control" name="credits" id="inputUrl" placeholder="{{ __('Enter Credits') }}" value="{{ $getValue('credits') }}">
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
                    <h5 class="card-title text-black">{{ __('Details') }}<span class="star" >*</span></h5>
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
                    <h5 class="card-title text-black">{{ __('Amount') }}<span class="star" >*</span></h5>
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
                    <h5 class="card-title text-black">{{ __('Unit') }}<span class="star" >*</span></h5>
                    <div class="card-body">
                      <div class="form-group">
                        <select class="form-control units" name="items[{{ $i }}][unit]" required
                            data-error-required="{{ __('Unit is required') }}">
                            <option value="" selected disabled>{{ __('Select Unit') }}</option>
                            <option value="{{ __('sq.m') }}">{{ __('sq.m') }}</option>
                            <option value="{{ __('m') }}">{{ __('m') }}</option>
                            <option value="{{ __('lump sum') }}">{{ __('lump sum') }}</option>
                            <option value="{{ __('leaf') }}">{{ __('leaf') }}</option>
                            <option value="{{ __('trip') }}">{{ __('trip') }}</option>
                            <option value="{{ __('set') }}">{{ __('set') }}</option>
                            <option value="{{ __('sheet') }}">{{ __('sheet') }}</option>
                            <option value="{{ __('unit') }}">{{ __('unit') }}</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div>
                    
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
                    <h5 class="card-title text-black">{{ __('Price Amount') }}<span class="star" >*</span></h5>
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

        <div class="col-lg-12 d-none d-md-block">
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

        <!-- ===== Mobile View Only (NEW - shows below rows) ===== -->
        <div class="col-12 d-md-none">
          <!-- 1. Mobile Add Row Button (FIRST) -->
          <div class="form-section mt-3">
            <div class="text-center">
              <button type="button" class="btn btn-primary btn-lg w-100" id="add-row-btn-mobile">
                <i class="fas fa-plus-circle me-2"></i>{{ __('Add New Item') }}
              </button>
            </div>
          </div>

          <!-- 2. Mobile Calculation Summary (SECOND) -->
          <div class="form-section mt-3">
            <div class="card shadow-sm">
              <div class="btn_div card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>{{ __('Cost Summary') }}</h5>
              </div>
              <div class="card-body">
                <div class="summary-row">
                  <div class="summary-label">{{ __('Total') }}:</div>
                  <div class="summary-value" id="totalDisplayMobile">0.00</div>
                </div>
                <div class="summary-row">
                  <div class="summary-label">{{ __('VAT 7%') }}:</div>
                  <div class="summary-value" id="taxDisplayMobile">0.00</div>
                </div>
                <div class="summary-row total-row">
                  <div class="summary-label" style="font-size:18px; color: #28a745;">{{ __('Total Price') }}:</div>
                  <div class="summary-value total-value" id="totalPriceDisplayMobile">0.00</div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Photo Upload Section (THIRD - NEW) -->
          <div class="form-section mt-3">
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
                  <div class="spinner-border text-primary" role="status"></div>
                  <div class="upload-progress">
                    <div class="upload-progress-bar" id="upload-progress-bar"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Mobile Action Buttons (FOURTH) -->
          <div class="form-section mt-3">
            <h5 class="btn_div form-section-title">
              <i class="fas fa-tasks me-2"></i>{{ __('Actions') }}
            </h5>
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="btn-group-vertical w-100" role="group">
                  <button type="submit" class="btn btn-primary btn-lg mb-3" formaction="{{ route('condo.preview') }}">
                    <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                  </button>
                  
                  <button type="submit" class="btn btn-success btn-lg mb-3" id="generate-btn-mobile">
                    <i class="fas fa-save me-2"></i>{{ __('Generate') }}
                  </button>
                  
                  <a href="{{ route('condo') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                  </a>
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
                <h5 class="card-title text-black">{{ __('Details') }}<span class="star" >*</span></h5>
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
                <h5 class="card-title text-black">{{ __('Amount') }}<span class="star" >*</span></h5>
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control amount" name="items[__INDEX__][amount]" placeholder=".00" required 
                    data-error-required="{{ __('Amount is required') }}"
                    data-error-number="{{ __('Please enter number only') }}">
                  </div>
                </div>
              </div>

              <div class="col-6 col-md-2 field-col">
                <h5 class="card-title text-black">{{ __('Unit') }}<span class="star" >*</span></h5>
                <div class="card-body">
                  <div class="form-group">
                      <select class="form-control units" name="items[{{ $i }}][unit]" required
                          data-error-required="{{ __('Unit is required') }}">
                          <option value="" selected disabled>{{ __('Select Unit') }}</option>
                          <option value="{{ __('sq.m') }}">{{ __('sq.m') }}</option>
                          <option value="{{ __('m') }}">{{ __('m') }}</option>
                          <option value="{{ __('lump sum') }}">{{ __('lump sum') }}</option>
                          <option value="{{ __('leaf') }}">{{ __('leaf') }}</option>
                          <option value="{{ __('trip') }}">{{ __('trip') }}</option>
                          <option value="{{ __('set') }}">{{ __('set') }}</option>
                          <option value="{{ __('sheet') }}">{{ __('sheet') }}</option>
                          <option value="{{ __('unit') }}">{{ __('unit') }}</option>
                      </select>
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
<script src="{{ asset('assets/js/chunked-upload.js') }}"></script>

@if(isset($restoredData) && $restoredData)
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Starting condo form restoration...');
    
    const restoredData = @json($restoredData ?? []);
    const restoredPhotos = @json($restoredPhotos ?? []);
    console.log('Restored data:', restoredData);
    console.log('Restored photos:', restoredPhotos);
    
    // Restore table items
    if (restoredData.items && Array.isArray(restoredData.items)) {
        console.log('Restoring', restoredData.items.length, 'items');
        
        const tbody = document.querySelector('#items-table tbody');
        if (tbody) {
            // Clear existing rows except the first one
            const rows = tbody.querySelectorAll('tr');
            for (let i = rows.length - 1; i > 0; i--) {
                rows[i].remove();
            }
            
            // Restore each item
            restoredData.items.forEach((item, index) => {
                let row;
                if (index === 0) {
                    // Use the first existing row
                    row = tbody.querySelector('tr');
                } else {
                    // Clone the first row for additional items
                    const firstRow = tbody.querySelector('tr');
                    row = firstRow.cloneNode(true);
                    tbody.appendChild(row);
                }
                
                if (row) {
                    // Set the row number
                    const noInput = row.querySelector('input[name="items[' + index + '][no]"]');
                    if (noInput) noInput.value = item.no || (index + 1);
                    
                    // Set other fields
                    const fields = ['details', 'amount', 'unit', 'material_cost', 'labor_cost', 'price_per_unit_total'];
                    fields.forEach(field => {
                        const input = row.querySelector('input[name="items[' + index + '][' + field + ']"]') ||
                                     row.querySelector('textarea[name="items[' + index + '][' + field + ']"]');
                        if (input && item[field] !== undefined) {
                            input.value = item[field];
                        }
                    });
                    
                    // Update name attributes for the new index
                    row.querySelectorAll('input, textarea, select').forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                        }
                    });
                }
            });
            
            // Trigger recalculation
            if (typeof recalculateAll === 'function') {
                recalculateAll();
            }
        }
    }
    
    // Restore photos
    if (restoredPhotos && restoredPhotos.length > 0) {
        const photoCountElement = document.getElementById('photo-count');
        const photoList = document.getElementById('photo-list');
        const newPhotosSection = document.getElementById('new-photos-section');
        const form = document.getElementById('condo-form');
        
        if (photoCountElement) {
            photoCountElement.textContent = restoredPhotos.length;
        }
        
        // Show new photos section
        if (newPhotosSection && restoredPhotos.length > 0) {
            newPhotosSection.style.display = 'block';
        }
        
        // Create a list of restored photos for display
        if (photoList) {
            photoList.style.display = 'block';
            restoredPhotos.forEach((photoPath, index) => {
                const photoItem = document.createElement('div');
                photoItem.className = 'photo-list-item restored-photo';
                photoItem.dataset.photoPath = photoPath;
                
                const filename = photoPath.split('/').pop();
                
                photoItem.innerHTML = `
                    <div class="photo-name" title="Restored Photo: ${filename}">
                        <i class="fas fa-image me-1 text-warning"></i>
                        ${filename}
                        <span class="format-badge badge-apple">RESTORED</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger delete-restored-photo-btn" data-photo-path="${photoPath}" title="Delete photo">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                
                // Add delete button event listener
                const deleteBtn = photoItem.querySelector('.delete-restored-photo-btn');
                deleteBtn.addEventListener('click', function() {
                    const photoPath = this.dataset.photoPath;
                    deleteRestoredPhoto(photoPath, photoItem);
                });
                
                photoList.appendChild(photoItem);
            });
        }
        
        // Create hidden inputs for restored photos
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
    
    console.log('Condo form restoration complete');
});

function deleteRestoredPhoto(photoPath, photoItem) {
    // Remove from DOM
    if (photoItem) {
        photoItem.remove();
    }
    
    // Remove the hidden input
    const hiddenInput = document.querySelector(`input[name="restored_photos[]"][value="${photoPath}"]`);
    if (hiddenInput) {
        hiddenInput.remove();
    }
    
    // Update photo count
    const photoCount = document.getElementById('photo-count');
    const remainingPhotos = document.querySelectorAll('.photo-list-item').length;
    if (photoCount) {
        photoCount.textContent = remainingPhotos;
    }
    
    // Hide section if no photos
    if (remainingPhotos === 0) {
        const newPhotosSection = document.getElementById('new-photos-section');
        if (newPhotosSection) {
            newPhotosSection.style.display = 'none';
        }
    }
}
</script>
@endif

<script>
// Photo upload placeholder functions (to prevent errors)
window.deleteAllNewPhotosAndClearSession = function() {
  const photoList = document.getElementById('photo-list');
  const photoCount = document.getElementById('photo-count');
  const photoInput = document.getElementById('photo-input');
  
  if (photoList) photoList.innerHTML = '';
  if (photoCount) photoCount.textContent = '0';
  if (photoInput) photoInput.value = '';
  
  // Clear uploaded photos array
  if (window.condoUploadedPhotos) {
    window.condoUploadedPhotos = [];
  }
  
  console.log('All new photos cleared');
};

window.deleteAllExistingPhotosAndClearSession = function() {
  const existingPhotosList = document.getElementById('existing-photos-list');
  if (existingPhotosList) {
    existingPhotosList.style.display = 'none';
  }
  console.log('All existing photos cleared');
};

// Store uploaded photos globally
window.condoUploadedPhotos = [];

// Basic photo upload handling
document.addEventListener('DOMContentLoaded', () => {
  const photoInput = document.getElementById('photo-input');
  const photoUploadArea = document.getElementById('photo-upload-area');
  const photoCount = document.getElementById('photo-count');
  const photoList = document.getElementById('photo-list');
  const newPhotosSection = document.getElementById('new-photos-section');
  const form = document.getElementById('condo-form');
  
  if (photoInput && photoUploadArea) {
    // Click to upload
    photoUploadArea.addEventListener('click', (e) => {
      if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
        photoInput.click();
      }
    });
    
    // File input change - EXACT COPY FROM HOME
    photoInput.addEventListener('change', (e) => {
      handlePhotos(e.target.files);
    });
    
    // Drag and drop
    photoUploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      photoUploadArea.classList.add('dragover');
    });
    
    photoUploadArea.addEventListener('dragleave', () => {
      photoUploadArea.classList.remove('dragover');
    });
    
    photoUploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      photoUploadArea.classList.remove('dragover');
      const files = e.dataTransfer.files;
      handlePhotos(files);
    });
  }
  
  function handlePhotos(files) {
    const validFiles = Array.from(files);
    
    if (validFiles.length === 0) {
      return;
    }
    
    processPhotos(validFiles);
  }
  
  function processPhotos(files) {
    files.forEach((file, index) => {
      const photoData = {
        id: 'photo-' + Date.now() + '-' + index,
        name: file.name,
        size: (file.size / (1024*1024)).toFixed(2) + ' MB',
        file: file  // Store the actual File object
      };
      
      window.condoUploadedPhotos.push(photoData);
      renderPhotoItem(photoData);
    });
    
    updatePhotoCounter();
    updatePhotosInput();
  }
  
  function renderPhotoItem(photo) {
    if (!photoList) return;
    
    const photoItem = document.createElement('div');
    photoItem.className = 'photo-list-item';
    photoItem.dataset.id = photo.id;
    
    photoItem.innerHTML = `
      <div class="photo-name" title="${photo.name}">
        <i class="fas fa-image me-1 text-primary"></i>
        ${photo.name}
      </div>
      <button type="button" class="btn btn-sm btn-danger delete-photo-btn" data-photo-id="${photo.id}" title="Delete photo">
        <i class="fas fa-trash"></i>
      </button>
    `;
    
    // Add delete button event listener
    const deleteBtn = photoItem.querySelector('.delete-photo-btn');
    deleteBtn.addEventListener('click', function() {
      const photoId = this.dataset.photoId;
      deletePhoto(photoId);
    });
    
    photoList.appendChild(photoItem);
    
    if (photoList && newPhotosSection) {
      photoList.style.display = 'block';
      newPhotosSection.style.display = 'block';
    }
  }
  
  function deletePhoto(photoId) {
    // Find and remove from uploaded photos array
    const index = window.condoUploadedPhotos.findIndex(p => p.id === photoId);
    if (index > -1) {
      window.condoUploadedPhotos.splice(index, 1);
    }
    
    // Remove from DOM
    const photoItem = document.querySelector(`.photo-list-item[data-id="${photoId}"]`);
    if (photoItem) {
      photoItem.remove();
    }
    
    // Update photo count
    const photoCount = document.getElementById('photo-count');
    if (photoCount) {
      photoCount.textContent = window.condoUploadedPhotos.length;
    }
    
    // Hide section if no photos
    if (window.condoUploadedPhotos.length === 0) {
      const newPhotosSection = document.getElementById('new-photos-section');
      if (newPhotosSection) {
        newPhotosSection.style.display = 'none';
      }
    }
    
    // Update the file input
    updatePhotosInput();
  }
  
  function updatePhotosInput() {
    // Clear existing file input
    if (photoInput) photoInput.value = '';
    
    // Create new DataTransfer object
    const dataTransfer = new DataTransfer();
    
    // Add all photos as files
    window.condoUploadedPhotos.forEach(photo => {
      if (photo.file) {
        dataTransfer.items.add(photo.file);
      }
    });
    
    // Update file input
    if (photoInput) {
      photoInput.files = dataTransfer.files;
      console.log('Updated file input with', dataTransfer.files.length, 'files');
    }
  }
  
  function updatePhotoCounter() {
    if (photoCount) {
      photoCount.textContent = window.condoUploadedPhotos.length;
    }
  }
  
  // Debug: Check files on form submit
  if (form) {
    form.addEventListener('submit', (e) => {
      const fileCount = photoInput ? photoInput.files.length : 0;
      console.log('Form submitting with', fileCount, 'files');
      console.log('condoUploadedPhotos array:', window.condoUploadedPhotos.length);
      
      if (fileCount === 0 && window.condoUploadedPhotos.length > 0) {
        console.error('FILES LOST! Array has files but input is empty!');
        // Try to restore
        updatePhotosInput();
      }
    });
  }
  
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
  }
});
</script>
@endpush
