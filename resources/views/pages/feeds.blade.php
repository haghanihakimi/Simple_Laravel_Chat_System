<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name', 'Messages')}} | Messages</title>
  </head>
  <body class="antialiased">
    <div id="app" :class="{'dark-theme': $store.getters.preferences.dark_mode, 'light-theme': !$store.getters.preferences.dark_mode}">
      @yield('feeds')
      <errors v-if="$store.getters.errors.popup"></errors>
      <profile v-if="$store.getters.profileActive"></profile>
      <profile-view :user-id="{{json_encode(auth()->user()->uid)}}" v-if="$store.getters.profileView"></profile-view>
      <contact-block-remove-alert v-if="$store.getters.contactActionAlert.status"></contact-block-remove-alert>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('css/defaults.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/preferences.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ProfileOverview.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profiles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peopleSearch.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profileView.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contactRequestsList.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ContactBlockRemoveAlert.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ConversationsView.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/darkTheme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightTheme.css') }}">
  </body>
</html>