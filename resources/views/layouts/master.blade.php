<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quotation')</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')
</head>

<body>

    {{-- header --}}
    @include('layouts.header')

    {{-- page content --}}
    @yield('content')

    {{-- footer --}}
    @include('layouts.footer')

    @stack('scripts')
</body>

</html>
