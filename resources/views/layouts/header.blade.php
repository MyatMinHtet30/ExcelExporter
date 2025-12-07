<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Booster is a bootstrap & laravel admin dashboard template">
    <meta name="keywords"
        content="admin, admin dashboard, admin panel, admin template, admin theme, bootstrap 4, laravel, crm, analytics, responsive, sass support, ui kits, web app, clean design, creative">
    <meta name="author" content="Themesbox17">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>Booster - Bootstrap + Laravel Admin Dashboard Template</title>

    <!-- Fevicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Start CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/chartist-js/chartist.min.css') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">

    <style>
        .lang-switch .btn-lang {
            padding: .45rem .9rem;
            border: 2px solid rgba(255, 255, 255, .9);
            border-radius: 9999px;
            font-weight: 700;
            letter-spacing: .5px;
            line-height: 1;
            color: #fff !important;
            background: rgba(0, 0, 0, .15);
            backdrop-filter: blur(6px);
            transition: .15s ease;
        }

        .lang-switch .btn-lang:hover {
            border-color: #fff;
            transform: translateY(-1px);
        }

        .lang-switch .dropdown-menu {
            min-width: 160px;
        }

        @media (max-width:576px) {
            .lang-switch {
                margin-right: .25rem;
            }

            .lang-switch .btn-lang {
                padding: .4rem .7rem;
            }
        }
    </style>
</head>

<body class="xp-horizontal">

    <!-- Start XP Container -->
    <div id="xp-container">

        <!-- Start XP Rightbar -->
        <div class="xp-rightbar">

            <!-- Start XP Headerbar -->
            <div class="xp-headerbar">

                <!-- Start XP Topbar -->
                <div class="xp-topbar">

                    <!-- Start XP Row -->
                    <div class="row">

                        <!-- Start XP Col -->
                        <div class="col-3 col-md-2 col-lg-2 order-1 order-md-1 align-self-center">
                            <div class="xp-logobar">
                                <a href="{{ route('dashboard') }}" class="xp-small-logo">
                                    <img src="{{ asset('assets/images/168HomeWebLogo.png') }}" class="img-fluid"
                                        alt="168 Home Logo"
                                        style="height:32px; object-fit:contain; transform:scale(1.5); transform-origin:center;">
                                </a>

                                <a href="{{ route('dashboard') }}" class="xp-main-logo">
                                    <img src="{{ asset('assets/images/168HomeWebLogo.png') }}" class="img-fluid"
                                        alt="168 Home Logo"
                                        style="height:42px; object-fit:contain; transform:scale(2); transform-origin:center;">
                                </a>
                            </div>
                        </div>
                        <!-- End XP Col -->

                        <!-- Start XP Col -->
                        <div class="col-12 col-md-5 col-lg-3 order-3 order-md-2">
                            {{-- <div class="xp-searchbar">
                                <form>
                                    <div class="input-group">
                                        <input type="search" class="form-control" placeholder="{{ __('Search') }}"
                                            aria-label="Search" aria-describedby="button-addon2">
                                        <div class="input-group-append">
                                            <button class="btn" type="submit" id="button-addon2">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div> --}}
                        </div>
                        <!-- End XP Col -->

                        <!-- Start XP Col -->
                        <div class="col-9 col-md-5 col-lg-7 order-2 order-md-3">
                            <div class="xp-profilebar text-right">
                                <ul class="list-inline mb-0">

                                    <!-- Language Switcher (ENG / THA) -->
                                    <!-- @php
                                        $locale = app()->getLocale();
                                        $currentLang = $locale === 'th' ? 'THA' : 'ENG';
                                    @endphp
                                    <li class="list-inline-item lang-switch">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle btn-lang" href="#" id="langDropdown"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                title="{{ __('Change language') }}">
                                                {{ $currentLang }}
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow"
                                                aria-labelledby="langDropdown">
                                                <a class="dropdown-item" href="{{ url('lang/en') }}">English</a>
                                                <a class="dropdown-item" href="{{ url('lang/th') }}">ไทย</a>
                                            </div>
                                        </div>
                                    </li> -->
                                    <li class="list-inline-item lang-switch">
                                        @php
                                            $locale = app()->getLocale();
                                            $toggleLang = $locale === 'th' ? 'en' : 'th'; // switch to the opposite
                                            $toggleLabel = $locale === 'th' ? 'THA' : 'ENG';
                                        @endphp
                                        <a class="btn-lang"
                                        href="{{ url('lang/' . $toggleLang) }}"
                                        title="{{ __('Change language') }}">
                                            {{ $toggleLabel }}
                                        </a>
                                    </li>

                                    <li class="list-inline-item mr-0">
                                        <div class="dropdown xp-userprofile">
                                            <a class="dropdown-toggle user-profile-img" href="#" role="button"
                                                id="xp-userprofile" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <img src="{{ asset('assets/images/topbar/Profile.webp') }}"
                                                alt="user-profile" class="rounded-circle img-fluid">
                                                <span class="xp-user-live"></span>
                                            </a>

                                            {{-- <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="xp-userprofile">
                                                <a class="dropdown-item"
                                                    href="#">{{ __('Welcome, :name', ['name' => 'John Doe']) }}</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-account mr-2"></i>
                                                    {{ __('Profile') }}</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="mdi mdi-credit-card mr-2"></i> {{ __('Billing') }}</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-settings mr-2"></i>
                                                    {{ __('Setting') }}</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-lock mr-2"></i>
                                                    {{ __('Lock Screen') }}</a>
                                                <a href="#" class="dropdown-item"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="mdi mdi-logout mr-2"></i> {{ __('Logout') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </div> --}}

                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="xp-userprofile">

                                                <a href="#" class="dropdown-item"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="mdi mdi-logout mr-2"></i> {{ __('Logout') }}
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-inline-item xp-horizontal-menu-toggle">
                                        <button type="button" class="navbar-toggle bg-transparent"
                                            data-toggle="collapse" data-target="#navbar-menu">
                                            <i class="mdi mdi-sort-variant font-24 text-white"></i>
                                        </button>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <!-- End XP Col -->

                    </div>
                    <!-- End XP Row -->

                </div>
                <!-- End XP Topbar -->

                <!-- Start XP Breadcrumbbar -->
                <div class="xp-breadcrumbbar text-center">
                    <h4 class="page-title">@yield('title')</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">168Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                    </ol>
                </div>
                <!-- End XP Breadcrumbbar -->

                <!-- Start XP Menubar -->
                <div class="xp-menubar text-left">
                    <nav class="xp-horizontal-nav xp-mobile-navbar xp-fixed-navbar">
                        <div class="collapse navbar-collapse" id="navbar-menu">
                            <ul class="xp-horizontal-menu">
                                <li class="dropdown">
                                    <a href="{{ route('dashboard') }}"><i
                                            class="mdi mdi-view-dashboard"></i><span>{{ __('Dashboard') }}</span></a>
                                </li>
                                <li class="menu-item-has-mega-menu">
                                    <a href="{{ route('home') }}"><i
                                            class="mdi mdi-layers"></i><span>{{ __('Home') }}</span></a>
                                </li>
                                <li>
                                    <a href="{{ route('condo') }}"><i
                                            class="mdi mdi-package-variant"></i><span>{{ __('Condo') }}</span></a>
                                </li>

                                <div class="mega-menu dropdown-menu">
                                    <ul class="mega-menu-row" role="menu">
                                        <li class="mega-menu-col col-md-4"></li>
                                    </ul>
                                </div>
                            </ul>
                        </div>
                    </nav>
                </div>
                <!-- End XP Menubar -->

            </div>
            <!-- End XP Headerbar -->
