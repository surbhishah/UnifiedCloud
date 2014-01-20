@extends('layouts.default')

@section('links')
    @parent

    {{ HTML::style('packages/css/landing.css')}}
    {{ HTML::style('packages/css/common.css')}}
@stop

@section('scripts')
    @parent

    {{ HTML::script('packages/bootstrap/js/modal.js' )}}
@stop

@section('content')
<!-- navigation to be seperated into another include file -->

    @include('layouts.nav.navigation')

    <!-- main content element -->
    <div class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h1 class="white brand brand-shadow">Kumo.</h1>
                    <h3 class="">Unifying every cloud you own into one. <br />Join us to enjoy our services!</h3>
                </div><!-- end col-->
            </div><!--end row -->
            <div class="row button-row">
                <div class="col-xs-12 text-center">

                    <button type="button" class="btn btn-custom btn-custom-common" data-toggle="modal" data-target="#SigninModal">
                        Sign in
                    </button>
                    or
                    <button type="button" class="btn btn-custom btn-custom-common" data-toggle="modal" data-target="#SignupModal">
                        Sign up
                   </button>


                </div>
            </div><!-- end button row-->

            <div class="row learn-more-row">
                <div class="col-xs-12 text-center">
                    <span class="glyphicon glyphicon-chevron-down"></span>
                </div>
            </div><!-- end learn more row-->
        </div><!-- end container -->
    </div><!-- end jumbotron -->
@include('landing.modal')
@stop
