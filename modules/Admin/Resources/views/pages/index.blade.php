{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
@extends('admin::layouts.master')

@section('title', 'Manage Pages')
@section('template-level-scripts')

@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') ) !!}
@stop

@section('content')
@include('admin::partials.breadcrumb')


<div id="errorMessage"></div>
@if(!empty(Auth::guard('admin')->user()->hasAdd))
<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::controller/page.add_new_page')!!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form">
            </a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        @include('admin::pages.create')
    </div>
</div>
@endif
<div class="portlet box yellow-gold edit-form-main display-hide">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>{!! trans('admin::controller/page.edit_page')!!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide" id="edit_form">

    </div>
</div>

<div class="row">
    <div class="col-md-12 manage-pages">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa {{$linkIcon}} font-blue-sharp"></i>
                    <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/page.view_pages')!!}</span>
                </div>
                <div class="actions">
                    @if(!empty(Auth::guard('admin')->user()->hasAdd))
                    <a href="javascript:;" class="btn blue btn-add-big btn-expand-form">
                        <i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::controller/page.add_new_page')!!} </span>
                    </a>
                    @endif
                </div>
            </div>

            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <input id="data-search" type="search" class="form-control" placeholder="Search">
                    </div>

                    <table class="table table-striped table-bordered table-hover" id="pages_datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th>#</th>
                                <th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>
                                <th>Page Name [ Slug ]<br>Description</th>
                                <th>URL</th>
                                <th></th>
                                <th width="10%">Status</th>
                                <th width="10%" class="sorting_disabled">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop


@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/pages.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('admintheme/tinymce/tinymce.min.js') ) !!}
{!! HTML::script( URL::asset('admintheme/tinymce/tinymce_editor.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery.slugify.js') ) !!}

@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.pages.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop