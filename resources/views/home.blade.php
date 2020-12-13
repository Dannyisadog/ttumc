@extends('layouts.app')

@section('title', '首頁')

@section('content')
<div class="container">
    <div id="home-page">
        <div class="carousel-wrapper">
            <carousel :images="carouselImages"></carousel>
        </div>
    </div>
</div>
@endsection

@section('js-down')
<script src="{{ asset('js/home.js') }}" defer></script>
@endsection
