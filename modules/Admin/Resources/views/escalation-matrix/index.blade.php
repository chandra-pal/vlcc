@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/escalation-matrix.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.escalationMatrixJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/escalation-matrix.escalation-matrix')]) !!}</span>
        </div>
<!--        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/escalation-matrix.escalation-matrix')]) !!} </span></a>
        </div>
        @endif-->
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="escalation-matrix-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th>{!! trans('admin::controller/escalation-matrix.center') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.member') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.package') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.weight-loss') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.weight-gain') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.escalation-date') !!}</th>
                        <th>{!! trans('admin::controller/escalation-matrix.ath-comment') !!}</th>
                        <th>View History</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
