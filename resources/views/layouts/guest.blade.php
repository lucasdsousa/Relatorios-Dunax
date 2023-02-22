<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dulub Área Interna') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
   
    <body class="font-sans text-gray-900 antialiased flex-1">
        <!--<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background-image: linear-gradient(to bottom, #4B5563, #000000)">
        <div class="flex flex-col items-center justify-center min-h-screen" style="background-image: linear-gradient(to bottom, #4B5563, #f7f2f2)">-->
        <div class="flex flex-col items-center justify-center min-h-screen" style="background-color: #4B5563;">
            <!--<div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
                <a href="/">
                    <img src="https://www.dulub.com.br/images/Logo%20-%20Rodap%C3%A9.svg" alt="Logo da Dulub" class="w-20 h-20">
                </a>
            </div>
            <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                            <img src="https://www.dulub.com.br/images/Logo%20-%20Rodap%C3%A9.svg" alt="Logo da empresa" class="block h-9 w-auto">
                    </a>
            </div>-->
            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}">
                     <img src="https://www.dulub.com.br/images/Logo%20-%20Rodap%C3%A9.svg" alt="Logo da empresa" class="h-10 w-auto mb-3 mt-3">
                 </a >
            </div>

            <!--<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>-->

            <!--<div class="w-full sm:max-w-md mt-6 mx-auto max-w-lg px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>-->

         </div>
    </body>
</html>
