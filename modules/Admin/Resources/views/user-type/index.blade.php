@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/user-type.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.userTypeJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::user-type.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light user-type">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View User Types</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form">
                <i class="fa fa-plus"></i> Add New User Type
            </a>
        </div>
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="types_datatable_ajax">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th>ID</th>
                        <th>User Type</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="id"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="name"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="description"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="priority"></td>
                        <td>
                            <select name="status" class="form-control form-filter input-sm width-auto">
                                <option value="">Select</option>
                                <option value="1"> {!! trans('admin::messages.active') !!}</option>
                                <option value="0"> {!! trans('admin::messages.inactive') !!}</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="Search"><i class="fa fa-search"></i></button>
                            <button class="btn btn-sm red blue filter-cancel margin-bottom-5" title="Reset"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop