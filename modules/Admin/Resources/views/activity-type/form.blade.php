<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/activity-type.activity-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('activity_type', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'activity_type', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/activity-type.activity-type')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/activity-type.activity-type')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/activity-type.activity-type')]) ])!!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/activity-type.ideal-calories-burn') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('calories', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control', 'id'=>'calories', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid calories.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/activity-type.ideal-calories-burn')]) ]) !!}
            <span class="help-block">eg: 500</span>
        </div>
        <label class="col-md-2 control-label" style="text-align: left;padding-left: 0px !important;">{!! trans('admin::controller/activity-type.per-min') !!}</label>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/activity-type.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>