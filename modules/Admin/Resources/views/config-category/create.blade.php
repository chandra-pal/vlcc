<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name' => trans('admin::controller/config-category.config-cat') ]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.config-categories.store'], 'method' => 'post', 'class' => 'form-horizontal config-category-form',  'id' => 'create-config-category', 'msg' => trans('admin::messages.added', ['name' => trans('admin::controller/config-category.config-cat') ] ) ]) !!}
        @include('admin::config-category.form')
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-8">
                            <button type="submit" class="btn green">{!! trans('admin::messages.submit') !!}</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
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