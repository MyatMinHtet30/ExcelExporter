<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Quotation')</title>

    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"  type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <!-- Add other common CSS files here -->

    @stack('styles') {{-- For page-specific styles --}}
</head>
<body>
    {{-- Include hearder --}}
    @include('layouts.header')


    {{-- Main content will go here --}}

      @yield('content')

    {{-- Include footer --}}
        @include('layouts.footer')


    {{-- For page-specific JS --}}
    @stack('scripts')
</body>

