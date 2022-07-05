@extends('pages.home')

@section('home')
  <div class="container-layer">&nbsp;</div>
  <div class="container">
    <div class="container-box">
      @include('components.GuestNavigation')
      @include('components.HomeSectionOne')
      @include('components.HomeSectionTwo')
      @include('components.HomePricingSection')
    </div>
  </div>
@endsection