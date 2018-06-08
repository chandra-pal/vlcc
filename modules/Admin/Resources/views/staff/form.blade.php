<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">First Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('first_name', null, ['maxlength'=>'60', 'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff.first_name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/staff.first_name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Last Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('last_name', null, ['maxlength'=>'60', 'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff.last_name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/staff.last_name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Gender <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('gender', 1, null, ['data-rule-required' => 'true']) !!} {!! trans('admin::controller/staff.male') !!}
                </label>
                <label class="radio-inline">{!! Form::radio('gender', 0, null) !!} {!! trans('admin::controller/staff.female') !!}
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Mobile Number<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('mobile_number', null, ['minlength'=>10,'class'=>'form-control', 'id'=>'mobile_number', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff.mobile_number')]), 'maxlength' => '10', 'data-rule-number'=>'10', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/staff.mobile_number')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Status </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>