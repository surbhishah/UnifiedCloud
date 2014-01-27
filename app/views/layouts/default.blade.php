<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{-- $title = "test title";  --}}
        <title>{{ $title }}</title>

        @section('links')
            {{ HTML::style('packages/bootstrap/css/bootstrap-yeti.css') }}
            {{ HTML::style('packages/css/stylesheets/common.css') }}
        @show
    </head>
    <body>
        @yield('content')
        @section('scripts')
            {{ HTML::script('packages/bootstrap/js/jquery-1.10.js')}}
            {{ HTML::script('packages/bootstrap/js/bootstrap.min.js')}}
            {{ HTML::script('packages/js/notify.js' )}}
        @show
        
        
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Kumo.</h4>
                        <p>A brainchild of Abhishek Nair, Jhalak Jain, Pooja Garg and Surbhi Shah.</p>
                    </div>
                    <div class="col-md-6 follow-us">
                        <h4>Follow us on.</h4>
                            <div id="fb"></div>
                            <!-- <span>Facebook.</span> -->
                        
                            <div id="twitter"></div>
                            <!-- <span>Twitter.</span> -->
                            <div id="github"></div>
                            <!-- <span>Github.</span> -->
                    </div>
                </div>
        </div>
        <!-- footer container -->
        </div>
        
    </body>
</html>

