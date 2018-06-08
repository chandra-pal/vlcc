@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/room.js') ) !!}
@stop

@section('scripts')
@parent

<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.roomJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')

@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::room.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View Rooms</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        @if(Auth::guard('admin')->user()->user_type_id != 4 || Auth::guard('admin')->user()->user_type_id != 8)
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add New Room </span></a>
        </div>
        @endif
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
<!--                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">-->
            </div>
            <table class="table table-striped table-bordered table-hover" id="RoomList">
                <thead>
                    <tr role="row" class="heading">
                        <th width='5%'>#</th>
                        <th width='5%'>ID</th>
                        <th>Center Name</th>
                        <th>Room Name</th>
                        <th width='20%'>Room Type(Male/Female/Common)</th>
                        <th width='20%'>Status</th>
                        <!--<th>Status</th>-->
                        <th width='20%'>Action</th>
                    </tr>
                    @include('admin::room.search')
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop
