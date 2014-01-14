@extends('layouts.master')

@section('content')
<div class="container">
<div class="row">
    <div class="col-xs-6 col-md-3">
        <h2>Dashboard</h2>
    </div>
    <div class="col-xs-12 col-md-9">
    </div>
</div>

<div class="row">
    <div class="col-xs-6 col-md-3">
    </div>
    <div class="col-xs-12 col-md-9">
            @include('dashboard.cloudControls')
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <div class="panel panel-default control-panel">
        <div class="btn-group-vertical btn-block">
            <button class="btn btn-primary ">
                <span class="pull-left">Clouds</span>
                <span class="glyphicon glyphicon-plus pull-right"></span>
            </button>
            @include('dashboard.clouds')
        <button class="btn btn-primary">
            <span class="pull-left">Account Settings</span>
            <span class="glyphicon glyphicon-cog pull-right"></span>
        </button>
        <button class="btn btn-default"></button>
        </div><!-- vertical btn group-->
        </div><!-- control panel-->
    </div>
    <div class="col-xs-12 col-md-9">
        <div class="panel panel-default file-explorer-panel">
            <div class="panel-heading">
                @include('dashboard.dashboardPanelHead')
            </div>
            <div class="panel-body">
                <table cellspacing="0" class="table table-striped table-condensed table-hover">
                     <thead>
                        <tr>
                             <td>
                                <strong>Name</strong>
                            </td>
                            <td>
                                <strong>Modified On</strong>
                            </td>
                            <td>
                                <strong>Size</strong>
                            </td>
                            <td>
                                <strong>Type</strong>
                            </td>
                        </tr>
                     </thead>
                        <tbody>
                           @include('dashboard.tableBody')
                        </tbody>
                </table>
            </div><!-- panel-body -->
            <div class="panel-footer"></div>
        </div>
    </div>
</div>
</div>
@endsection
