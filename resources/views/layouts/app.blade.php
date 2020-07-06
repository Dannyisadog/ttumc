<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('partials._head')
<body>
    @include('sweetalert::alert')
    @yield('js-up')
    <div id="app">
        @include('partials._nav')

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('js-down')
</body>
</html>
