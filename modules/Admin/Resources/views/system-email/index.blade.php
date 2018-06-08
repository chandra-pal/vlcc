@extends('admin::layouts.master')

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

<div id="ajax-response">
</div>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::controller/system-email.view-add-edit') !!}
        </div>
    </div>

    <div class="portlet-body form">
        <form action="#" class="form-horizontal">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label col-md-4">
                                {!! trans('admin::messages.select-name', ['name'=>trans('admin::controller/system-email.system-email')]) !!}
                            </label>
                            <div class="col-md-8">
                                <div id="dropDownForm">
                                    @include('admin::system-email.dropdown')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::system-email.create')
@endif

<div id="edit_form">

</div>

@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('admintheme/tinymce/tinymce.min.js') ) !!}
{!! HTML::script( URL::asset('admintheme/tinymce/tinymce_editor.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/system-email.js') ) !!}

@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.systemEmailJs.init();
    });
</script>
@stop