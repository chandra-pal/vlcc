@extends('admin::layouts.master')
@section('page-level-styles')
{!! HTML::style( URL::asset('admintheme/pages/css/profile.css') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/members.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery.flot.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery.flot.pie.min.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.membersJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        siteObjJs.admin.membersJs.pieChartData = ["{!! $bcaData['fat_mass'] !!}", "{!! $bcaData['protein'] !!}", "{!! $bcaData['water'] !!}", "{!! $bcaData['mineral'] !!}"];
        siteObjJs.admin.membersJs.memberId = "{!! $memberId !!}";
        siteObjJs.admin.membersJs.initPieCharts();        
    });

</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>
<!--@if(!empty(Auth::guard('admin')->user()->hasAdd))
@ include('admin::member-activity.create')
@endif-->
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<!-- BEGIN PAGE BASE CONTENT -->
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet bordered">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <img src="../assets/pages/media/profile/profile_user.jpg" class="img-responsive" alt="" onerror="this.src='{{ URL::asset('images/default-user-icon-profile.png ') }}'"> </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name">
                        {{ $memberData['first_name'] ? $memberData['first_name'] : "" }}
                        {{ $memberData['last_name'] ? $memberData['last_name'] : "" }}
                    </div>
                    <div class="profile-usertitle-job">{{$memberDetails['age']}} &bull; {{$memberDetails['gender']}} </div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <!--                <div class="profile-userbuttons">
                                    <button type="button" class="btn btn-circle green btn-sm">Follow</button>
                                    <button type="button" class="btn btn-circle red btn-sm">Message</button>
                                </div>-->
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="javascript:;" title='Client ID'>
                                <i class="icon-user-following"></i> {{$memberDetails['crm_customer_id'] ? $memberDetails['crm_customer_id']: "NA"}} </a>
                        </li>
                        <li>
                            <a href="javascript:;" title='Mobile Number'>
                                <i class="icon-screen-smartphone"></i> {{$memberDetails['mobile_number'] ? $memberDetails['mobile_number']: ""}} </a>
                        </li>
                        <!--                        <li>
                                                    <a href="javascript:;">
                                                        <i class="icon-info"></i> Help </a>
                                                </li>-->
                    </ul>
                </div>
                <!-- END MENU -->
            </div>
            <!-- END PORTLET MAIN -->


        </div>
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light portlet-fit bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-layers font-dark"></i>
                                <span class="caption-subject font-dark bold uppercase">BCA Data</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="donut" class="chart"> </div>
                        </div>
                        <div class="portlet-body" style="background-color: #252B32; padding: 1px 20px 7px !important;">
                            <div class="row number-stats">
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="title"> Fat Mass </div>
                                            <div class="number" style="color: #FFCE5D;"> {{isset($bcaData['fat_mass']) ? $bcaData['fat_mass']: ""}} kg </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="title"> Protein </div>
                                            <div class="number" style="color: #1D8945;"> {{isset($bcaData['protein']) ? $bcaData['protein']: ""}} kg </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="title"> Water </div>
                                            <div class="number" style="color: #3762BE;"> {{isset($bcaData['water']) ? $bcaData['water']: ""}} ltr </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="stat-left">
                                        <div class="stat-number">
                                            <div class="title"> Mineral </div>
                                            <div class="number" style="color: #69398D;"> {{isset($bcaData['mineral']) ? $bcaData['mineral']: ""}} kg </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row number-stats margin-bottom-30">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-left">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Lean Body Mass </div>
                                            <div class="number"> {{isset($bcaData['lean_body_mass']) ? $bcaData['lean_body_mass']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-right">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar2"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Percent Body Fat </div>
                                            <div class="number"> {{isset($bcaData['percent_body_fat']) ? $bcaData['percent_body_fat']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row number-stats margin-bottom-30">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-left">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Viscral Fat Level </div>
                                            <div class="number"> {{isset($bcaData['visceral_fat_level']) ? $bcaData['visceral_fat_level']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-right">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar2"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Basal metabolic Rate </div>
                                            <div class="number"> {{isset($bcaData['basal_metabolic_rate']) ? $bcaData['basal_metabolic_rate']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row number-stats margin-bottom-30">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-left">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Body Mass Index </div>
                                            <div class="number"> {{isset($bcaData['body_mass_index']) ? $bcaData['body_mass_index']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-right">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar2"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Latest Weight </div>
                                            <div class="number"> {{isset($bcaData['current_weight']) ? $bcaData['current_weight']: ""}} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row number-stats margin-bottom-30">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-left">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Recommended Calories </div>
                                            <div class="number"> 
                                                @if($recommendedCalories['calories'] == null || $recommendedCalories['calories'] == 0 )
                                                -
                                                @else
                                                {{$recommendedCalories['calories']}}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="stat-right">
                                        <div class="stat-chart">
                                            <div id="sparkline_bar2"></div>
                                        </div>
                                        <div class="stat-number">
                                            <div class="title"> Latest Activity </div>
                                            <div class="number"> 
                                                @if($latestActivity == null )
                                                -
                                                @else
                                                {{$latestActivity}}
                                                @endif
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
        <!-- END BEGIN PROFILE SIDEBAR -->
    </div>
</div>
<!-- END PAGE BASE CONTENT -->

<div class="portlet light col-lg-12">
    <div class="page-title">
        <h2>View Member Packages</h2>
    </div>
    
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="members-packages-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th width='1%'>ID</th>
                        <th>CRM Package Id</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Payment</th>
                        <th>Payment Made</th>
                    </tr>
                </thead>
                <tbody id="members-packages-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
