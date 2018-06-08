@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/deviation.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.deviationJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}


<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/deviation.deviation')]) !!}</span>
            <input type="hidden" value="{!!$newDate!!}" id="newDate" name="newDate">
            <input type="hidden" value="{!!$scheduleType!!}" id="schedule_type" name="schedule_type">
            <input type="hidden" value="{!!$diteticianId!!}" id="diteticianId" name="diteticianId">
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <!--<a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/deviation.deviation')]) !!} </span></a>-->
        </div>
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="deviation-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th>{!! trans('admin::controller/deviation.deviation-id') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/deviation.client-name') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/deviation.schedule-type') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/deviation.recommended-calories') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/deviation.consumed-calories') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/deviation.deviation') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/deviation.action') !!}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
