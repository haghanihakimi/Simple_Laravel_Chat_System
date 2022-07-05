<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name', 'Sign Up')}} | Sign Up</title>
    <link rel="stylesheet" href="{{ asset('css/defaults.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/TwoFaChallenge.css') }}">
    <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
  </head>
  <body class="antialiased">
    <div id="app">
      @yield('twoFaChallenge')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
  </body>
</html>