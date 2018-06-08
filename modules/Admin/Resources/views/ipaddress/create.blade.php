<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>Add New IP Address
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.ipaddress.store'], 'data-user-id' => '', 'id' => 'ipaddress-form', 'method' => 'post', 'class' => 'form-horizontal panel config-category-form']) !!}
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label">IP Address <span class="required" aria-required="true">*</span></label>
                <div class="col-md-4">
                    {!! Form::text('ip_address', null, ['class'=>'form-control', 'maxlength' => 15, 'id'=>'ip_address', 'data-rule-required' => 'true', 'data-msg-required' =>'Please enter valid IP Address' ]) !!}
                    <span class="help-block">Eg. 127.0.0.1, 192.168.151.190 </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Status </label>
                <div class="col-md-4">
                    {!!  Form::select('status', [0 => 'Pending', 1 =>'Accepted', 2 => 'Rejected'], 1, ['class'=>'select2me form-control'])!!}
                </div>
            </div>
        </div>
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green">Submit</button>
                    <button type="button" class="btn default btn-collapse-form">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>