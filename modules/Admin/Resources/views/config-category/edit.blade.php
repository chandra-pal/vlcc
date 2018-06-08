<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>{!! trans('admin::messages.edit-name',['name' => trans('admin::controller/config-category.config-cat') ]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($configCategory, ['route' => ['admin.config-categories.update', $configCategory->id], 'method' => 'put', 'class' => 'form-horizontal panel config-category-form','id'=>'edit-config-category', 'msg' => trans('admin::messages.updated', ['name' => trans('admin::controller/config-category.config-cat')] ) ]) !!}
        @include('admin::config-category.form')

        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-8">
                            <button type="submit" class="btn green">{!! trans('admin::messages.save') !!}</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form-edit">{!! trans('admin::messages.cancel') !!}</button>
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