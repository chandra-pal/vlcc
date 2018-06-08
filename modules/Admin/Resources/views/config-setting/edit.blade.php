<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>{!! trans('admin::messages.edit-name', ['name' => trans('admin::controller/config-setting.conf-set')]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($configSetting, ['route' => ['admin.config-settings.update', $configSetting->id], 'method' => 'put', 'class' => 'form-horizontal panel config-setting-form','id'=>'edit-config-setting', 'msg' => trans('admin::messages.updated', ['name' => trans('admin::controller/config-setting.conf-set') ] ) ]) !!}
        {!! Form::hidden('id', $configSetting->id) !!}
        @include('admin::config-setting.form')
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-9">
                            <button class="btn green" type="submit">{!! trans('admin::messages.save') !!}</button>
                            <button class="btn default btn-collapse btn-collapse-form-edit" type="button">{!! trans('admin::messages.cancel') !!}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>

        </div>
        {!! Form::close() !!}
    </div>
</div>