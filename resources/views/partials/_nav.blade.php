<nav class="navbar navbar-expand-md navbar-dark bg-black ">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <div class="brand-name">TTUMC</div>
            <img src="{{url('images/logo/logo_white.png')}}" width="30">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('schedule') }}">{{ __('schedule') }}</a>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('register') }}</a>
                        </li>
                    @endif
                @else
                    @admin

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('schedulemgm') }}">{{ __('manage') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usermanagement') }}">{{ __('users') }}</a>
                    </li>
                    @else
                    @endadmin
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('bandlist') }}">{{ __('bands') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('band') }}" class="nav-link" href="#" role="button" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>