@extends('layouts.app')

@section('title', '登入')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card login-card" style="background-color: #222222; color: #fff; margin-top: 30px;">
                <div class="card-header" style="display:flex; justify-content:center;">{{ __('登入') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="login-content">
                            <div class="account-row">
                                <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="password-row">
                                <input id="password" type="password" placeholder="密碼" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
    
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="login-button-row">
                                <button type="submit" class="btn btn-primary btn-block btn-blue">
                                    {{ __('登入') }}
                                </button>
                            </div>
    
                            <div class="login-footer-row">
                                <div class="remember-wrapper">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="margin-right: 5px">記住我
                                </div>
                                <div class="password-forget-wrapper">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">
                                            {{ __('忘記密碼?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
