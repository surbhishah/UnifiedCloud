@extends('layouts.default')


@section('links')
    @parent

    {{ HTML::style('packages/css/landing.css')}}
    {{ HTML::style('packages/css/common.css')}}
@stop

@section('scripts')
    @parent

    {{ HTML::script('packages/js/signin.js' )}}
@stop

@section('content')
    @include('layouts.nav.navigation')

           
    <div class="jumbotron">
        <div class="container">

            <div class="row">
                <div class="col-md-4 col-md-offset-4 card">
                     <h2>Sign In</h2>
                     <form role="form" method="post" action="{{ route('sign_in') }}">
                        <div class="form-group">
                            @if(Input::old())
                                {{-- */ $email = Input::old('email'); /*--}}
                            @else
                                {{-- */ $email = ""; /*--}}
                            @endif
                            <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ $email }}">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-custom-small">Submit</button>
                    </form>
                </div>
            </div>
            <!-- this element is hidden and rendered as popover using bootstrap and jquery -->
           @if(Session::has('message'))
                <p class="signin_error hide" rel="popover">{{ Session::get('message') }}</p>
            @endif

        </div><!-- container -->
    </div><!-- jumbotron -->


@stop
