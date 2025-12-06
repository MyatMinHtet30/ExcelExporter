<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('Login_title', 'App Name')</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

    @yield('login_content') {{-- Insert page-specific content here --}}

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            // specifically check for logout flash
            const logoutMsg = {!! json_encode(session('logout_success')) !!};

            if (logoutMsg) {
                Swal.fire({
                    icon: 'success',
                    title: @json(__('Logged out')),
                    text: logoutMsg,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        })();
    </script>
</body>
</html>
