<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--*/ $title = "test title"; /* --}}
        <title>{{ $title }}</title>

        @section('links')
            {{ HTML::style('packages/bootstrap/css/bootstrap-yeti.css') }}
        @show
    </head>
    <body>
        @yield('content')
        @section('scripts')
            {{ HTML::script('packages/bootstrap/js/jquery-1.10.js')}}
            {{ HTML::script('packages/bootstrap/js/bootstrap.min.js')}}
            {{ HTML::script('js/main.js')}}
        @show
    </body>
</html>
