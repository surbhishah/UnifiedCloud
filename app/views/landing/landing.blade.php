@extends('layouts.default')

@section('links')
    @parent

   <!--  {{ HTML::style('packages/css/landing.css')}} -->
    {{ HTML::style('packages/css/stylesheets/landing.css')}}
@stop

@section('scripts')
    @parent

    {{ HTML::script('packages/bootstrap/js/modal.js' )}}
    {{ HTML::script('packages/js/landing.js' )}}
@stop

@section('content')
<!-- navigation to be seperated into another include file -->

    @include('layouts.nav.navigation')
    @if(Session::has('message'))    
        <div class="notification">
          {{ Session::get('message') }}  
        </div>
    @endif
    <img src="packages/img/wcloud-256-1.png" id="box">
    <img src="packages/img/wcloud-256-2.png" id="box2">
    <img src="packages/img/wcloud-256-1.png" id="box3">
    <img src="packages/img/wcloud-256-2.png" id="box4">
    <!-- main content element -->
    <div class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h1 class="brand brand-shadow">Kumo.</h1>
                    <h3 class="">Bringing all your clouds together. <br />Join us for FREE! No ads Ever!</h3>
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
            <div class="learn-more-divider"></div>
            <div class="row learn-more">
                <div class="col-xs-12 text-center">
                    <h3>Learn more.</h3>
                    <span class="glyphicon glyphicon-circle-arrow-down"></span>
                </div>
            </div><!-- end learn more row-->
        </div><!-- end container -->
    </div><!-- end jumbotron -->
@include('landing.modal')
<div class="container features">
    <!-- feauter-left one access -->
    <div class="row">
        <div class="col-md-7">
            <h1>Access.</h1>
            <p>With so many clouds in the market it has become difficult for all of us
                to manage our accounts. And some of us are so smart that we have made many accounts on the same service (Email IDs are free, right?! ;D). With our fab app you can access all your clouds all at one place.</p>        
        </div>
        <div class="col-md-5">
            <img src="packages/img/secure.png" class="img-responsive" alt="500x500">
        </div>
    </div>
    <!-- feature security end -->
    
    <hr class="learn-more-divider">

    <!-- feature-right privacy -->
    <div class="row">
        <div class="col-md-5"><img src="" alt="" class="img-responsive"></div>
        <div class="col-md-7">
            <h1>Total Privacy</h1>
            <p>Scared of NSA snooping on your data! No worries Man! With our path breaking service, we encrypt your data with the uncrackable AES encryption algorithm and 
                we are so unbelievably honest that we don't even save your pass keys! Yeah you read it right!</p>
        </div>
    </div>
    <!-- feature privacy end -->
    
    <hr class="learn-more-divider">

    <!-- feauter-left security -->
    <div class="row">
        <div class="col-md-7">
            <h1>Instant Searching.</h1>
            <p>Do you forget where you keep your files or make errors while typing (All of us hate our typos!). Then our app is the right one for you. Our jaw dropping search speeds will boggle your mind!</p>        
        </div>
        <div class="col-md-5">
            <img src="packages/img/secure.png" class="img-responsive" alt="500x500">
        </div>
    </div>
    <!-- feature security end -->
    
    <hr class="learn-more-divider">

    <!-- feature-right sharing -->
    <div class="row">
        <div class="col-md-5"><img src="" alt="" class="img-responsive"></div>
        <div class="col-md-7">
            <h1>Sharing.</h1>
            <p>Share your files with your friends on other clouds as well.</p>
        </div>
    </div>
    <!-- feature privacy end -->
    <hr class="learn-more-divider">
    <!-- feauter-left security -->
    <div class="row">
        <div class="col-md-7">
            <h1>Auto-syncing</h1>
            <p>Do you want your cloud to catch up with the changes you make to your files? Then download our desktop app and leave all your worries to us.</p>        
        </div>
        <div class="col-md-5">
            <img src="packages/img/secure.png" class="img-responsive" alt="500x500">
        </div>
    </div>
    <!-- feature security end -->
    
</div>
<!-- feature container -->
@stop
