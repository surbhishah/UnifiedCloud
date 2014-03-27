@extends('layouts.default')

@section('content')
@if(Session::has('message'))    
    <div class="alert alert-success alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('message') }}
    </div>
@endif
@include('layouts.nav.navigation')
<div class="container">

    @include('dashboard.cloudSelectModal')
    @include('dashboard.fileUploadModal')
    @include('dashboard.shareModal')
    @include('dashboard.encryptionModal')
    <div class="loading"></div>

<div class="row">
    <div class="col-md-2">
        <h3 class="dashboard-heading"></h3>
    </div>
    <!-- col -->
    <div class="col-md-10">
        @include('dashboard.cloudControls')
    </div>
    <!-- col -->
</div>
<!-- row -->
<div class="row">
    <div class="col-xs-2 col-md-2" id="side-bar">

        <ul class="side-bar-buttonlist" >
            <li id="side-bar-header"></li>
            <li id="all-cloud-header" class="disabled-li">
                <span class="glyphicon glyphicon-cloud float-left" ></span>
                <span class="hidden-xs hidden-sm">Clouds</span>
                <span class="clear-both"></span>
            </li>
            
            @include('dashboard.clouds')
            <li id="add-cloud" data-toggle="modal" data-target="#SelectModal">
                <span class="glyphicon glyphicon-plus-sign float-left" ></span>
                <span class="hidden-xs hidden-sm">Clouds</span>
                <span class="clear-both"></span>
            </li>
            <li id="show-shared-files">
                <span class="glyphicon glyphicon-link float-left"></span>
                <span class="hidden-xs hidden-sm">Shared files</span>
                <span class="clear-both"></span>
            </li>
            <li id="show-shared-files-with-me">
                <span class="glyphicon glyphicon-link float-left"></span>
                <span class="hidden-xs hidden-sm">To me</span>
                <span class="clear-both"></span>
            </li>
            <!-- <li id="global-settings">
                <span class="glyphicon glyphicon-cog float-left"></span>
                <span class="hidden-xs hidden-sm">Settings</span>
                <span class="clear-both"></span>
            </li> -->
        </ul>
        <!-- ul  -->
    </div>
    <!-- side-bar col-->
    <div class="col-xs-10 col-md-10" id="file-explorer">
        <div class="row">
            @include('dashboard.dashboardPanelHead')
        </div>
        <!-- dashboardPanelHead -->
        <div class="row">
            <table cellspacing="0" class="table tablesorter">
               <thead>
                    <tr>
                        <th>Name</th>
                        <th>Modified On</th>
                        <th>Size</th>
                        <th>Type</th>
                    <tr>
                </thead>
                <tbody>
                    @include('dashboard.tableBody')
                </tbody>
            </table>
        </div>
        <!-- row -->
    </div>

</div>

<div id="cwd"></div>
</div>
<!-- container -->
@endsection

@section('scripts')
@parent
{{ HTML::script('packages/js/dashboard.js')}}
{{ HTML::script('packages/js/jquery-dateformat.js')}}
{{ HTML::script('packages/bootstrap/js/modal.js' )}}
{{ HTML::script('packages/js/jquery.tablesorter.js' )}}
{{ HTML::script('packages/js/jquery.ui.position.js' )}}
{{ HTML::script('packages/js/jquery.contextMenu.js' )}}
{{ HTML::script('packages/js/bootstrap-typeahead.js' )}}
<script>
    $('#file-search').typeahead({
        ajax : {
            url : 'search/files/1',
            displayField : "file_name",
            preProcess : function(data) {
                console.log(data);
                return data;
            }
        }
    });
</script>
@stop

@section('links')
@parent
{{ HTML::style('packages/css/stylesheets/dashboard.css') }}
{{ HTML::style('packages/css/stylesheets/jquery.contextMenu.css') }}
@stop