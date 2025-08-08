@extends('layouts.master')

@section('title', 'Generate Excel Form')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/forminput-table.css') }}   " rel="stylesheet" type="text/css" />
@endpush

@section('content')
  <form action="#" method="POST">
        @csrf
            <div class="xp-contentbar">

                <!-- Start XP Row -->
                <div class="row">

                    <!-- Start XP Col -->
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="card m-b-20">
                            <div class="card-header bg-white">
                                <h5 class="card-title text-black">Customer name</h5>
                                <div class="card-body">
                                <div class="form-group">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="inputText" id="inputText" placeholder="Enter Customer name" >
                                    <button type="button" class="btn btn-outline-secondary mic-btn" title="Speak" onclick="startDictation(this)">
                                      <i class="fas fa-microphone"></i>
                                    </button>
                                  </div>
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
                                <h5 class="card-title text-black">Address</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="inputEmail" id="inputEmail" placeholder="Enter Address">
                                      <button type="button" class="btn btn-outline-secondary mic-btn" title="Speak" onclick="startDictation(this)">
                                        <i class="fas fa-microphone"></i>
                                      </button>
                                    </div>
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
                                <h5 class="card-title text-black">Job Name</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="inputPassword" id="inputPassword" placeholder="Job Name">
                                      <button type="button" class="btn btn-outline-secondary mic-btn" title="Speak" onclick="startDictation(this)">
                                        <i class="fas fa-microphone"></i>
                                      </button>
                                    </div>
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
                                <h5 class="card-title text-black">Quotation number</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="inputNumber" id="inputNumber" placeholder="Enter QN">
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
                                <h5 class="card-title text-black">Date</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="inputDate" id="inputDate">
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
                                <h5 class="card-title text-black">Payent term</h5>
                                <div class="card-body">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="inputSearch" id="inputSearch" placeholder="Enter PT">
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
                                <h5 class="card-title text-black">Credits</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="inputUrl" id="inputUrl" placeholder="Enter Credits">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End XP Col -->

                    <div id="rows-container">
                      <div class="col-lg-12">
                        <div class="card m-b-2 item-row">
                          <div class="card-header bg-white">
                            <div class="row">

                              <div class="col-lg-1 col-md-1 col-2">
                                <h5 class="card-title text-black">No</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control serial" value="1" readonly>
                                  </div>
                                </div>
                              </div>

                              <div class="col-lg-5 col-md-4 col-10">
                                <h5 class="card-title text-black">Details</h5>
                                <div class="card-body">
                                  <div class="input-group">
                                    <input type="text" class="form-control detail" name="details" placeholder="Details">
                                    <button type="button" class="btn btn-outline-secondary mic-btn" title="Speak" onclick="startDictation(this)">
                                      <i class="fas fa-microphone"></i>
                                    </button>
                                  </div>
                                </div>
                              </div>


                              <div class="col-lg-1 col-md-2 col-3">
                                <h5 class="card-title text-black">Amount</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control amount" name="amount" placeholder=".00">
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-1 col-3">
                                <h5 class="card-title text-black">Units</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control units" name="units" placeholder="Units">
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-2 col-4">
                                <h5 class="card-title text-black">Material Cost</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control material" name="material_cost" placeholder=".00">
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-2 col-4">
                                <h5 class="card-title text-black">Labor Cost</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control labor" name="labor_cost" placeholder=".00">
                                  </div>
                                </div>
                              </div>

                              <div class="d-none d-md-block col-md-8"></div>

                              <div class="col-md-2 col-4">
                                <h5 class="card-title text-black">price amount</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control price-per-unit" name="price_per_unit" placeholder=".00">
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-2 col-4">
                                <h5 class="card-title text-black">Subtotal</h5>
                                <div class="card-body">
                                  <div class="form-group">
                                    <input type="text" class="form-control subtotal" name="subtotal" placeholder=".00" >
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
                    </div>


                    <div class="col-lg-12 ">
                        <div class="card m-b-20">
                            <div class="card-header bg-white">
                              <div class="row">



                                <div class="col-md-8 col-5">
                                  <div class="card-body">
                                      <div class="form-group">
                                          <button type="button" class="btn btn-primary" id="add-row-btn">+ Add Row</button>
                                      </div>

                                      <div class="form-group pt-5">
                                        <button type="button" class="btn btn-success" id="generate-btn">Generate</button>
                                      </div>
                                  </div>
                                </div>


                                <div class="col-md3 col-4">
                                  <div class="text-end" style="min-width: 200px; margin-left: auto">

                                    <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                        <strong>Total:</strong>
                                        <span class="ms-2" id="totalDisplay">0.00</span>
                                    </div>

                                    <div class="d-flex justify-content-between border p-2 mb-2 bg-light">
                                        <strong>Tax (7%):</strong>
                                        <span class="ms-2" id="taxDisplay">0.00</span>
                                    </div>

                                    <div class="d-flex justify-content-between border p-2 bg-light">
                                        <strong>Total Price:</strong>
                                        <span class="ms-2" id="totalPriceDisplay">0.00</span>
                                    </div>
                                </div>

                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/condo/condo-form.js') }}"></script>
@endpush
