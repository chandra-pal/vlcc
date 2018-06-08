<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">IP Address <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {{--*/ $editVal = ''; if(!empty($ipAddress->ip_address)){ $editVal =$ipAddress->ip_address; } /*--}}
            {!! Form::text('ip_address', null, ['data-ipaddress-val' =>$editVal,  'class'=>'form-control', 'maxlength' => 15, 'id'=>'ip_address']) !!}
            <span class="help-block">Eg. 127.0.0.1, 192.168.151.190 </span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Status </label>
        <div class="col-md-4">
            {!!  Form::select('status', [0 => 'Pending', 1 =>'Accepted', 2 => 'Rejected'], null, ['required', 'class'=>'select2me form-control'])!!}
        </div>
    </div>
</div>