<div class="portlet box blue add-form-main measurements-record-form stop-open">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.measurements')]) !!} 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.cpr.store-measurements-records'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal measurements-records-form',  'id' => 'create-measurements-records', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        <fieldset>
            @include('admin::cpr.measurements-form')      
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>