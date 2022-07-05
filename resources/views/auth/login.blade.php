@extends('pages.login')

@section('login')
  <div class="container">
    <div class="container-box">
      @include('components.GuestNavigation')
      @include('components.login')
    </div>
    {{-- @include('components.footer') --}}
  </div>
@endsection