<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
    </head>

    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @else
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $title ?? 'Home' }}
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="mx-auto sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        @stack('modals')
        <!-- resources/views/layouts/app.blade.php -->

        @livewireScriptConfig
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (data) => {
                    toastr.success(data[0].message);
                });
            });
        </script>

        @if (session()->has('message'))
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('message') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

    </body>

</html>
