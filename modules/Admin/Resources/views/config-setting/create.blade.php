<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name' => trans('admin::controller/config-setting.conf-set')]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.config-settings.store'], 'method' => 'post', 'class' => 'form-horizontal config-setting-form',  'id' => 'create-config-setting', 'msg' => trans('admin::messages.added', ['name' => trans('admin::controller/config-setting.conf-set') ] ) ]) !!}
        @include('admin::config-setting.form')
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-9">
                            <button class="btn green" type="submit">{!! trans('admin::messages.submit') !!}</button>
                            <button class="btn default btn-collapse btn-collapse-form" type="button">{!! trans('admin::messages.cancel') !!}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>