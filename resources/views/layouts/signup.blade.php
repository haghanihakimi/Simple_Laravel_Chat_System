@extends('pages.signup')

@section('signup')
  <div class="container">
    <div class="container-box">
      @include('components.GuestNavigation')
      @include('components.signup')
    </div>
    {{-- @include('components.footer') --}}
  </div>
@endsection