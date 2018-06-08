<div class="form-body">
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/member-activity-recommendation.activity-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('activity_type_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member-activity-recommendation.activity-type') ])] + $activityList, null,['class'=>'select2me form-control', 'id' => 'activity_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Message Type.']) !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/member-activity-recommendation.recommendation-date-time') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('recommendation_date', null , ['minlength'=>2,'maxlength'=>50,'class'=>'form-control recommendation-date-time','readonly'=>'true', 'id'=>'recommendation_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.recommendation-date-time')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/member-activity-recommendation.recommendation-date-time')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/member-activity-recommendation.recommendation-date-time')]) ])!!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/member-activity-recommendation.duration') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('duration', null, ['minlength'=>1,'maxlength'=>3,'class'=>'form-control', 'id'=>'duration', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid Duration.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.duration')]) ]) !!}
            <span class="help-block">eg: 15 minutes</span>
        </div>
        <label class="col-md-2 control-label" style="text-align: left;padding-left: 0px !important;">{!! trans('admin::controller/member-activity-recommendation.minute') !!}</label>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/member-activity-recommendation.calories-recommended') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('calories_recommended', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control','readonly'=>'true', 'id'=>'calories_recommended', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid Calories Recommended.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.calories-recommended')]) ]) !!}
        </div>
    </div>


</div>