<nav class="navbar navbar-expand-md navbar-dark bg-black shadow">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{url('images/logo/logo_white.png')}}" width="50">大同大學熱音社
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
                    <a class="nav-link" href="{{ route('feedback') }}">{{ __('許願池') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('schedule') }}">{{ __('練團表') }}</a>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('登入') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('註冊') }}</a>
                        </li>
                    @endif
                @else
                    @admin

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('schedulemgm') }}">{{ __('練團表管理') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usermanagement') }}">{{ __('使用者管理') }}</a>
                    </li>
                    @else
                    @endadmin
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('bandlist') }}">{{ __('樂團列表') }}</a>
                    </li>
                    <li class="nav-item dropdown">
                    
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('band') }}">
                                {{ __('所屬樂團') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('登出') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>