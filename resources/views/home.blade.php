@extends('layouts.app')

@section('title', '首頁')

@section('content')
<div class="container">
    <home-page id="home-page"></home-page>
</div>
@endsection

@section('js-down')
<script src="{{ asset('js/home.js') }}" defer></script>
@endsection
