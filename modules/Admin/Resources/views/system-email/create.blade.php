<div class="portlet box blue add-form-main">
    <div class="portlet-title toggleable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::controller/system-email.system-email-details') !!}
        </div>
        <div class="tools">
                <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.system-emails.store'], 'method' => 'post', 'class' => 'form-horizontal system-email-form',  'id' => 'create-system-email', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/system-email.system-email')]) ]) !!}
        @include('admin::system-email.form')
        <div class="row form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn green" type="submit">{!! trans('admin::messages.submit') !!}</button>
                            <button class="btn default btn-collapse" type="button">{!! trans('admin::messages.cancel') !!}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>