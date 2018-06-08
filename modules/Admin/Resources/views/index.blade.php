@extends('admin::layouts.master')
@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/metronic/slimscroll.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/metronic/waypoints.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/metronic/counterup.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/metronic/metronic_app.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery.flot.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery.flot.pie.min.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        var data = ["{!! $categoryWiseCustomers['success_regular'] !!}", "{!! $categoryWiseCustomers['unsuccess_regular'] !!}", "{!! $categoryWiseCustomers['success_irregular'] !!}", "{!! $categoryWiseCustomers['unsuccess_irregular'] !!}"];
        var yellow = "#FFCE5D";
        var green = "#1D8945";
        var blue = "#3762BE";
        var purple = "#69398D";
        var catplot = $.plot('#donut', data, {
            series: {
                pie: {
                    show: true,
                    innerRadius: 0.7,
                },
            },
            colors: [yellow, green, blue, purple],
            legend: {
                show: true,
            },
            grid: {
                hoverable: true
            }
        });
        if (isNaN(catplot.getData()[0].percent)) {
            var canvas = catplot.getCanvas();
            var ctx = canvas.getContext("2d");  //canvas context
            var x = canvas.width / 2;
            var y = canvas.height / 2;
            ctx.font = '15pt Calibri';
            ctx.textAlign = 'center';
            ctx.fillText('Data not found!', x, y);
        }
    });
</script>
@stop


@section('content')
<div class="page-head">
    <div class="page-title">
        <h1>Dashboard</h1>
    </div>
</div>
<div class="row">
    @if($user_type_id == 11)
    <script type="text/javascript">
        var adminUrl = '{!!URL::to("/admin")!!}';
        window.location = adminUrl + '/view-todays-sessions';
    </script>
    @endif
    @if($user_type_id == 4 || $user_type_id == 6 || $user_type_id == 7 || $user_type_id == 8  || $user_type_id == 9)
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$sessionBookings}}">0</span>
                </div>
                <div class="desc"> Today's Appointments </div>
            </div>
            @if($sessionBookings != 0)
            <a class="more" href="{!! URL::to('admin/view-todays-sessions') !!}"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @else
            <a class="more" href="javascript:void();"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @endif
        </div>
    </div>
    @endif
    
    @if($user_type_id == 4 || $user_type_id == 7 || $user_type_id == 8 || $user_type_id == 9)
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat red">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $escalation }}">0</span>
                </div>
                <div class="desc"> Escalations </div>
            </div>
            @if($escalation != 0)
            <a class="more" href="{!! URL::to('admin/escalation-matrix') !!}"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @else
            <a class="more" href="javascript:void();"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $deviation }}">0</span>
                </div>
                <div class="desc"> Deviation </div>
            </div>
            @if($deviation != 0)
            <a class="more" href="{!! URL::to('admin/member-diet-deviation') !!}"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @else
            <a class="more" href="javascript:void();"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @endif
        </div>
    </div>
    @endif
    
    @if($user_type_id == 4 || $user_type_id == 7 || $user_type_id == 8 || $user_type_id == 9 || $user_type_id == 5)
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $members }}">0</span>
                </div>
                <div class="desc"> My Clients </div>
            </div>
            @if($members != 0)
            <a class="more" href="{!! URL::to('admin/members') !!}"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @else
            <a class="more" href="javascript:void();"> View list
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
            @endif
        </div>
    </div>
    @endif
    
    @if($user_type_id!=5 && $user_type_id!=11)
    <div class="col-md-6">
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light portlet-fit bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-dark"></i>
                                <span class="caption-subject font-dark bold uppercase">Categorywise Customers</span>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <a href="{!! URL::to('admin/reports/categorywise-customers') !!}"><div id="donut" class="chart"> </div></a>
                        </div>

                        <div class="portlet-body" style="background-color: #252B32; padding: 1px 20px 7px !important;">
                            <div class="row number-stats">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="number" style="color: #FFCE5D;"> {{isset($categoryWiseCustomers['success_regular']) ? $categoryWiseCustomers['success_regular']: ""}}  </div>
                                            <div class="title"> Successful Regular(SR) </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="number" style="color: #1D8945;"> {{isset($categoryWiseCustomers['unsuccess_regular']) ? $categoryWiseCustomers['unsuccess_regular']: ""}} </div>
                                            <div class="title"> Unsuccessful Regular (USR) </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="number" style="color: #3762BE;"> {{isset($categoryWiseCustomers['success_irregular']) ? $categoryWiseCustomers['success_irregular']: ""}} </div>
                                            <div class="title"> Successful Irregular (SIR) </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="number" style="color: #69398D;"> {{isset($categoryWiseCustomers['unsuccess_irregular']) ? $categoryWiseCustomers['unsuccess_irregular']: ""}} </div>
                                            <div class="title"> Unsuccessful Irregular (USIR) </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
<div class="clearfix"></div>

@stop

