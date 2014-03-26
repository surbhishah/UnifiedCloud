@extends('layouts.default')


@section('links')
@parent

{{ HTML::style('packages/css/landing.css')}}
{{ HTML::style('packages/css/common.css')}}
@stop

@section('scripts')
    @parent

    {{ HTML::script('packages/js/signup.js' )}}
@stop

@section('content')
@include('layouts.nav.navigation')

<div class="jumbotron">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 card">
               <h2>Sign Up</h2>
               <form role="form" method="post" action="{{ route('sign_up') }}">
                <div class="form-group">
                    @if(Input::old())
                        {{-- */ $first_name = Input::old('first_name'); /*--}}
                    @else
                        {{-- */ $first_name = ""; /*--}}
                    @endif
                    <input type="firstname" name="first_name" class="form-control" 
                    placeholder="First Name" value="{{ $first_name }}">
                </div>
                <div class="form-group">
                    @if(Input::old())
                                {{-- */ $last_name = Input::old('last_name'); /*--}}
                            @else
                                {{-- */ $last_name = ""; /*--}}
                            @endif
                    <input type="lastname" name="last_name" class="form-control" 
                    placeholder="Last Name" value=" {{ $last_name }}">
                </div>
                <div class="form-group">
                    @if(Input::old())
                                {{-- */ $email = Input::old('email'); /*--}}
                            @else
                                {{-- */ $email = ""; /*--}}
                            @endif
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ $email }}">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-custom-small">Submit</button>
            </form>
        </div>
    </div>
                    
    <p class="first_name_error hide" rel="popover">{{ $errors->first('first_name') }}</p>
    <p class="last_name_error hide" rel="popover">{{ $errors->first('last_name') }}</p>
    <p class="email_error hide" rel="popover">{{ $errors->first('email') }}</p>
    <p class="password_error hide" rel="popover">{{ $errors->first('password') }}</p>

                
</div><!-- container -->
</div><!-- jumbotron -->
@stop
