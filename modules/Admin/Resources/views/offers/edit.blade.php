<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>{!! trans('admin::messages.edit-name', ['name' => trans('admin::controller/offers.offers') ]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($offers, ['route' => ['admin.offers.update', $offers->id], 'method' => 'put', 'class' => 'form-horizontal panel offers-form','id'=>'edit-offers', 'files' => 'true', 'msg' => trans('admin::messages.updated',['name'=>trans('admin::controller/offers.offers')]) ]) !!}
        {!! Form::hidden('remove', '', ['class'=>'form-control', 'id'=>'remove']) !!}
        @include('admin::offers.form')
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green">{!! trans('admin::messages.save') !!}</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form-edit">{!! trans('admin::messages.cancel') !!}</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>