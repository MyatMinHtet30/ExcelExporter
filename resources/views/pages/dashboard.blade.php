@extends('layouts.master')

@php
    use App\Models\Home;
    use App\Models\Condo;

    $homeCount  = Home::count();
    $condoCount = Condo::count();
@endphp

@section('title', __('Dashboard'))

@section('content')

<style>
    .hover-smooth {
        transition: 0.25s ease;
        cursor: pointer;
    }

    .hover-smooth:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.07);
    }

    /* Smaller cards */
    .stats-card {
        padding: 20px !important;
        border-radius: 12px !important;
        border: 1px solid #e9e9e9;
    }

    .stats-body {
        padding: 20px !important;
    }

    /* Smaller icon circle */
    .stats-icon {
        width: 48px !important;
        height: 48px !important;
    }

    .btn-group-stack a {
        display: block;
        width: 100%;
        margin-bottom: 6px;
    }

    @media (min-width: 576px) {
        .btn-group-stack a {
            display: inline-block;
            width: auto;
            margin-bottom: 0;
            margin-right: 6px;
        }
    }

    /* Reduce column spacing */
    .col-md-6 {
        margin-bottom: 20px;
    }
</style>

<div class="xp-contentbar">
    <div class="row justify-content-center">

        <!-- HOME -->
        <div class="col-md-6 col-lg-4">
            <div class="card stats-card hover-smooth">

                <div class="card-body text-center stats-body">

                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary-rgba rounded-circle stats-icon">
                            <i class="mdi mdi-home-outline font-24 text-primary"></i>
                        </span>
                    </div>

                    <h6 class="card-title text-black mb-1">Home Excel Forms</h6>
                    <p class="text-muted mb-2" style="font-size: 13px;">
                        Total home records created
                    </p>

                    <h2 class="mb-2" style="font-size: 34px;">
                        {{ number_format($homeCount ?? 0) }}
                    </h2>

                    <div class="mt-3 btn-group-stack">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">View List</a>
                        <a href="{{ route('home.create') }}" class="btn btn-primary btn-sm">Create New Home</a>
                    </div>

                </div>
            </div>
        </div>

        <!-- CONDO -->
        <div class="col-md-6 col-lg-4">
            <div class="card stats-card hover-smooth">

                <div class="card-body text-center stats-body">

                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary-rgba rounded-circle stats-icon">
                            <i class="mdi mdi-city font-24 text-primary"></i>
                        </span>
                    </div>

                    <h6 class="card-title text-black mb-1">Condo Excel Forms</h6>
                    <p class="text-muted mb-2" style="font-size: 13px;">
                        Total condo records created
                    </p>

                    <h2 class="mb-2" style="font-size: 34px;">
                        {{ number_format($condoCount ?? 0) }}
                    </h2>

                    <div class="mt-3 btn-group-stack">
                        <a href="{{ route('condo') }}" class="btn btn-outline-secondary btn-sm">View List</a>
                        <a href="{{ route('condo.create') }}" class="btn btn-primary btn-sm">Create New Condo</a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
