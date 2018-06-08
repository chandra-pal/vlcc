@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('global/scripts/datatable.js') ) !!}
{!! HTML::script( URL::asset('js/admin/links.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.links.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
@section('content')
@include('admin::partials.breadcrumb')
<div id="errorMessage"></div>
@if(!empty(Auth::guard('admin')->user()->hasAdd))
<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::controller/links.add_new_link') !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form">
            </a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        @include('admin::links.create',['categoryNames'=>$categoryNames,'userTypes'=>$userTypes,'selectedUserTypes'=>$selectedUserTypes])
    </div>
</div>
@endif
<div class="portlet box yellow-gold edit-form-main display-hide">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>{!! trans('admin::controller/links.edit_link') !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide" id="edit_form">

    </div>
</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/links.view_links') !!}</span>
        </div>
        <div class="actions">
            @if(!empty(Auth::guard('admin')->user()->hasAdd))
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form">
                <i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::controller/links.add_new_link') !!} </span>
            </a>
            @endif
        </div>
    </div>
    @if(session()->has('ok'))
    @include('admin::partials/message', ['type' => 'success', 'message' => session('ok')])
    @endif
    <div class="portlet-body">
        <div class="table-container">
            @if(!empty(Auth::guard('admin')->user()->hasEdit))
            <div class="table-actions-wrapper">
                {!!  Form::select('status', ['' => 'Select', 1 => trans('admin::messages.active'), 0 => trans('admin::messages.inactive') ], Input::old('status'), ['required', 'class'=>'table-group-action-input form-control input-inline input-small input-sm', 'data-actionType'=>'group', 'data-actionField'=>'status']) !!}
                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
            </div>
            @endif
            <table class="table table-striped table-bordered table-hover" id="linkmanagement-table">
                <thead>
                    <tr role="row" class="heading">
                        <th><input type="checkbox" class="group-checkable"></th>
                        <th>Id</th>
                        <th>Category</th>
                        <th>Category id</th>
                        <th>Link</th>
                        <th>Page Name</th>
                        <th>Pagination</th>
                        <th>Display Order</th>
                        <th>Status</th>
                        <th>Status</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                    @include('admin::links.search',['categoryNames'=>$categoryNames,'linkList'=>$linkList])
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
@stop
