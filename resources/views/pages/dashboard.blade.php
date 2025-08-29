@extends('layouts.master')

{{-- DEBUG locale: {{ app()->getLocale() }} --}}

@section('title', __('Dashboard'))

@section('content')
    <!-- Start XP Contentbar -->
    <div class="xp-contentbar">

        <div class="row">
            <!-- Total Projects -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="xp-widget-box">
                            <div class="float-left">
                                <h4 class="xp-counter text-primary">2580</h4>
                                <p class="mb-0 text-muted">{{ __('Total Projects') }}</p>
                            </div>
                            <div class="float-right">
                                <div class="xp-widget-icon xp-widget-icon-bg bg-primary-rgba">
                                    <i class="mdi mdi-file-document font-30 text-primary"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="xp-widget-box">
                            <div class="float-left">
                                <h4 class="xp-counter text-success">55790</h4>
                                <p class="mb-0 text-muted">{{ __('Total Revenue') }}</p>
                            </div>
                            <div class="float-right">
                                <div class="xp-widget-icon xp-widget-icon-bg bg-success-rgba">
                                    <i class="mdi mdi-currency-usd font-30 text-success"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Clients -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="xp-widget-box">
                            <div class="float-left">
                                <h4 class="xp-counter text-warning">930</h4>
                                <p class="mb-0 text-muted">{{ __('Total Clients') }}</p>
                            </div>
                            <div class="float-right">
                                <div class="xp-widget-icon xp-widget-icon-bg bg-warning-rgba">
                                    <i class="mdi mdi-account-multiple font-30 text-warning"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Visitors -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="xp-widget-box">
                            <div class="float-left">
                                <h4 class="xp-counter text-danger">2750</h4>
                                <p class="mb-0 text-muted">{{ __('Total Visitors') }}</p>
                            </div>
                            <div class="float-right">
                                <div class="xp-widget-icon xp-widget-icon-bg bg-danger-rgba">
                                    <i class="mdi mdi-eye font-30 text-danger"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <!-- Daily Revenue -->
            <div class="col-md-12 col-lg-12 col-xl-6">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black mb-0">{{ __('Daily Revenue') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="xp-chart-label">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-primary"></i>{{ __('Series A') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-success"></i>{{ __('Series B') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-danger"></i>{{ __('Series C') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-warning"></i>{{ __('Series D') }}</p>
                                </li>
                            </ul>
                        </div>
                        <div id="xp-chartist-advanced-smil-animations"
                            class="ct-chart ct-golden-section xp-chartist-advanced-smil-animations"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="col-md-12 col-lg-12 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black mb-0">{{ __('Monthly Revenue') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="xp-chart-label">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-primary"></i>{{ __('Series A') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-success"></i>{{ __('Series B') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-danger"></i>{{ __('Series C') }}</p>
                                </li>
                            </ul>
                        </div>
                        <div id="xp-chartist-stacked-bar" class="ct-chart ct-golden-section xp-chartist-stacked-bar"></div>
                    </div>
                </div>
            </div>

            <!-- Yearly Revenue -->
            <div class="col-md-12 col-lg-12 col-xl-3">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black mb-0">{{ __('Yearly Revenue') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="xp-chart-label">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-primary"></i>{{ __('Series A') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-success"></i>{{ __('Series B') }}</p>
                                </li>
                                <li class="list-inline-item">
                                    <p><i class="mdi mdi-circle-outline text-danger"></i>{{ __('Series C') }}</p>
                                </li>
                            </ul>
                        </div>
                        <div id="xp-chartist-donut-fill-rather-chart"
                            class="ct-chart ct-golden-section xp-chartist-donut-fill-rather-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects -->
        <div class="row">
            <div class="col-md-12 col-lg-8 col-xl-8 align-self-center">
                <div class="card bg-white m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black mb-0">{{ __('Our Projects') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Index') }}</th>
                                        <th>{{ __('Project Name') }}</th>
                                        <th>{{ __('Earnings') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Reviews') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><a href="javascript:void(0);">{{ __('Web Designing') }}</a></td>
                                        <td>$100</td>
                                        <td>01/05/2018</td>
                                        <td>30/07/2018</td>
                                        <td>
                                            <i class="mdi mdi-star text-warning"></i>
                                            <i class="mdi mdi-star text-warning"></i>
                                            <i class="mdi mdi-star text-warning"></i>
                                            <i class="mdi mdi-star text-warning"></i>
                                            <i class="mdi mdi-star text-warning"></i>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-success">{{ __('Completed') }}</span>
                                        </td>
                                    </tr>
                                    <!-- Add more rows here and wrap text with __() as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Statistics -->
            <div class="col-md-12 col-lg-4 col-xl-4">
                <div class="card m-b-30">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-black mb-0">{{ __('Project Statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="xp-progressbar">
                            <p>{{ __('WordPress') }}<span class="pull-right">85%</span></p>
                            <p>{{ __('HTML') }}<span class="pull-right">30%</span></p>
                            <p>{{ __('Android') }}<span class="pull-right">75%</span></p>
                            <p>{{ __('UI/UX') }}<span class="pull-right">50%</span></p>
                            <p>{{ __('SEO') }}<span class="pull-right">65%</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chart.js/chart-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/chartjs-init.js') }}"></script>
@endpush
