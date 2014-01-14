@extends('layouts.default')


@section('links')
    @parent

    {{ HTML::style('packages/css/landing.css')}}
    {{ HTML::style('packages/css/common.css')}}
@stop


@section('content')
    @include('layouts.nav.navigation')

    <div class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <h2>Sign In</h2>
                </div>
                <div class="col-md-4 col-md-offset-4">
                     <form role="form">
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-custom">Submit</button>
                    </form>
                </div>
            </div>
        </div><!-- container -->
    </div><!-- jumbotron -->
@stop
