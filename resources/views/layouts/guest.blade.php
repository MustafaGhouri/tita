<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      :root{ --primary:#522583; }
      /* Global reset to avoid Bootstrap-like styles messing inputs */
      input[type="text"],input[type="email"],input[type="password"]{
        appearance:none; -webkit-appearance:none; background:#fff;
      }
    </style>
  </head>
  <body class="font-sans text-slate-800 antialiased bg-gray-50">
    <div class="min-h-screen w-full flex items-center justify-center px-4">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8">
          {{ $slot }}
        </div>
        <div class="text-center text-xs text-gray-400 mt-6">
          © {{ now()->year }} {{ config('app.name','App') }}. All rights reserved.
        </div>
      </div>
    </div>
  </body>
</html>
