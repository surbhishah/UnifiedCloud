@extends('layouts.default')

@section('content')
@include('layouts.nav.navigation')
<div class="container">

    @include('dashboard.cloudSelectModal')
    @include('dashboard.fileUploadModal')
    {{-- print_r($clouds) --}}
<!-- <div class="row">
    <div class="col-xs-6 col-md-3">
        
    </div>
    <div class="col-xs-12 col-md-9">
    </div>
</div>
 -->
<div class="row">
    <div class="col-xs-6 col-md-3">
        <h3 class="dashboard-heading">Dashboard</h3>
    </div>
    <div class="col-xs-12 col-md-9">
            @include('dashboard.cloudControls')
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <div class="panel panel-default user-panel">
        <div class="btn-group-vertical btn-block">
            <button class="btn btn-custom ">
                <span class="pull-left btn-label-lg">Clouds</span>
                <span class="glyphicon glyphicon-plus pull-right" data-toggle="modal" data-target="#SelectModal"></span>
            </button>
            @include('dashboard.clouds')
        <button class="btn btn-custom ">
            <span class="pull-left btn-label-lg">Account Settings</span>
            <span class="glyphicon glyphicon-cog pull-right"></span>
        </button>
        </div><!-- vertical btn group-->
        </div><!-- control panel-->
    </div>
    <div class="col-xs-12 col-md-9">
        <div class="panel panel-default file-explorer-panel">
            <div class="panel-heading">
                @include('dashboard.dashboardPanelHead')
            </div>
            <div class="panel-body">
                <table cellspacing="0" class="table" id="file-explorer">
                     <thead>
                        <tr>
                             <td>
                                Name
                            </td>
                            <td>
                                Modified On
                            </td>
                            <td>
                                Size
                            </td>
                            <td>
                                Type
                            </td>
                        </tr>
                     </thead>
                        <tbody>
                           @include('dashboard.tableBody')
                        </tbody>
                </table>
            </div><!-- panel-body -->
            
        </div>
    </div>
</div>
<div id="cwd"></div>
</div>
@endsection

@section('scripts')
    @parent
    {{ HTML::script('packages/js/dashboard.js')}}
    {{ HTML::script('packages/js/jquery-dateformat.js')}}
    {{ HTML::script('packages/bootstrap/js/modal.js' )}}
    {{ HTML::script('packages/js/notify.js' )}}
@stop

@section('links')
    @parent
    {{ HTML::style('packages/css/dashboard.css') }}
    {{ HTML::style('packages/css/common.css') }}
@stop