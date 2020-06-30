@extends('layouts.app')

@section('title', '建議與問題')

@section('content')
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('建議與問題') }}</div>
    
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
                                    {{ __('送出') }}
                                </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection