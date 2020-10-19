@extends('layouts.app')

@section('title', '建議與問題')

@section('content')
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (Auth::check())
                <div class="card" style="background-color: #34383d; color: #fff;">
                    <div class="card-header">{{ __('許願池') }}</div>
    
                    <div class="card-body">
                        @if(session()->has('success-msg'))
                            <div class="alert alert-success">
                                {{ session()->get('success-msg') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('createfeedback') }}">
                            @csrf
    
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input id="feedback" type="text" class="form-control @error('email') is-invalid @enderror" name="feedback" required autofocus>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                {{ __('許願') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                @foreach ($feedbacks as $feedback)
                <div class="card" style="background-color: #34383d; color: #fff; margin-top :20px">
                    <div class="card-header">
                        @admin{{$feedback->user->name}}的@endadmin願望
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12" style="font-size: 20px">
                                {{$feedback->content}}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection