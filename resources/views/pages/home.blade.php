<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name', 'Home')}} | Home</title>
    <link rel="stylesheet" href="{{ asset('css/defaults.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  </head>
  <body class="antialiased">
    <div id="app">
      @yield('home')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
  </body>
</html>