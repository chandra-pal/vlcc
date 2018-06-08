@if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($systemEmail->created_by == Auth::guard('admin')->user()->id)))
<div class="portlet box yellow-gold edit-form-main">
    @else
    <div class="portlet box blue edit-form-main">
        @endif
        <div class="portlet-title toggleable">
            <div class="caption">
                <i class="fa fa-plus"></i>{!! trans('admin::controller/system-email.system-email-details') !!}
            </div>
            <div class="tools">
                <a href="javascript:;" class="collapse box-expand-form"></a>
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($systemEmail, ['route' => ['admin.system-emails.update', $systemEmail->id], 'method' => 'put', 'class' => 'form-horizontal panel system-email-form','id'=>'edit-system-email', 'msg' => trans('admin::messages.updated',['name'=>trans('admin::controller/system-email.system-email')]) ]) !!}
            {!! Form::hidden('id', $systemEmail->id) !!}
            @include('admin::system-email.form')
            <div class="row form-group">
                <label class="control-label col-md-2">{!! trans('admin::controller/system-email.status') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-10">
                    <div class="radio-list">
                        <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                        <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
                    </div>
                </div>
            </div>
            <div class="row form-actions">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                @if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($systemEmail->created_by == Auth::guard('admin')->user()->id)))
                                <button class="btn green" type="submit">{!! trans('admin::messages.save') !!}</button>
                                @endif
                                <button class="btn default btn-collapse" type="button">{!! trans('admin::messages.cancel') !!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>