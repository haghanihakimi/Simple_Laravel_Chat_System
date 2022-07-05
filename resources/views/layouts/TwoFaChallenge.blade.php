@extends('pages.TwoFaChallenge')

@section('twoFaChallenge')
  <div class="container">
    <div class="container-box">
      @include('components.GuestNavigation')
      @include('components.TwoFaComponent')
    </div>
  </div>
@endsection