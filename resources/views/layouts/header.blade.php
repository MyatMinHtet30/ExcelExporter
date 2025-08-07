<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Booster is a bootstrap & laravel admin dashboard template">
    <meta name="keywords" content="admin, admin dashboard, admin panel, admin template, admin theme, bootstrap 4, laravel, crm, analytics, responsive, sass support, ui kits, web app, clean design, creative">
    <meta name="author" content="Themesbox17">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>Booster - Bootstrap + Laravel Admin Dashboard Template</title>

    <!-- Fevicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Start CSS -->
    <!-- Chartist Chart CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/chartist-js/chartist.min.css') }}">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">

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
                            <!-- Start XP Logobar -->
                            <div class="xp-logobar">
                                <a href="index.html" class="xp-small-logo"><img src="assets/images/mobile-logo.svg" class="img-fluid" alt="logo"></a>
                                <a href="index.html" class="xp-main-logo"><img src="assets/images/logo.svg" class="img-fluid" alt="logo"></a>
                            </div>                        
                            <!-- End XP Logobar -->
                        </div> 
                        <!-- End XP Col -->

                        <!-- Start XP Col -->
                        <div class="col-12 col-md-5 col-lg-3 order-3 order-md-2">
                            <div class="xp-searchbar">
                                <form>
                                    <div class="input-group">
                                      <input type="search" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                                      <div class="input-group-append">
                                        <button class="btn" type="submit" id="button-addon2">GO</button>
                                      </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End XP Col -->

                        <!-- Start XP Col -->
                        <div class="col-9 col-md-5 col-lg-7 order-2 order-md-3">
                            <div class="xp-profilebar text-right">
                                <ul class="list-inline mb-0">

                                    <li class="list-inline-item mr-0">
                                        <div class="dropdown xp-userprofile">
                                            <a class="dropdown-toggle user-profile-img" href="#" role="button" id="xp-userprofile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/topbar/user.jpg" alt="user-profile" class="rounded-circle img-fluid"><span class="xp-user-live"></span></a>

                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="xp-userprofile">
                                                <a class="dropdown-item" href="#">Welcome, John Doe</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-account mr-2"></i> Profile</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-credit-card mr-2"></i> Billing</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-settings mr-2"></i> Setting</a>
                                                <a class="dropdown-item" href="#"><i class="mdi mdi-lock mr-2"></i> Lock Screen</a>
                                                <!-- <form id="logout-form" action="{{ route('logout') }}" method="POST"> -->
                                                    <!-- @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="mdi mdi-logout mr-2"></i> Logout
                                                    </button>
                                                </form> -->
                                                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="mdi mdi-logout mr-2"></i> Logout
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>                                   
                                    </li>
                                                                        <li class="list-inline-item xp-horizontal-menu-toggle">
                                        <button type="button" class="navbar-toggle bg-transparent" data-toggle="collapse" data-target="#navbar-menu">
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
                        <li class="breadcrumb-item"><a href="#">Booster</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                      </ol>                
                </div>
                <!-- End XP Breadcrumbbar -->        

                <!-- Start XP Menubar -->                    
                <div class="xp-menubar text-left">

                    <!-- Start XP Nav -->
                    <nav class="xp-horizontal-nav xp-mobile-navbar xp-fixed-navbar">

                        <div class="collapse navbar-collapse" id="navbar-menu">
                          <ul class="xp-horizontal-menu">

                            <li class="dropdown">
                              <a href="{{ route('dashboard') }}" ><i class="mdi mdi-view-dashboard"></i><span>Dashboard</span></a>
                            </li>
                            <li class="menu-item-has-mega-menu">
                              <a href="{{ route('home') }}"><i class="mdi mdi-layers"></i><span>Home</span></a>
                            </li>
                            <li>
                              <a href="{{ route('condo') }}"><i class="mdi mdi-package-variant"></i><span>Condo</span></a>
                            </li>
                              <div class="mega-menu dropdown-menu">
                                <ul class="mega-menu-row" role="menu">
                                  <li class="mega-menu-col col-md-4">
                                </ul>
                              </div>
                            </li> 
                          </ul>
                        </div>

                    </nav>
                    <!-- End XP Nav -->

                </div>
                <!-- End XP Menubar -->

            </div>
            <!-- End XP Headerbar -->