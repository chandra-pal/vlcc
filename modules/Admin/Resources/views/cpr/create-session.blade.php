<div class="portlet box blue add-form-main bca-record-form stop-open">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.session-records')]) !!} 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.cpr.store-session-records'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal session-records-form',  'id' => 'create-session-records', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        <fieldset>
            @include('admin::cpr.session-form')
            <div class="form-actions">
                <div class="col-md-6">
                    <button type="submit" class="btn green">{!! trans('admin::messages.submit') !!}</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                </div>
            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>