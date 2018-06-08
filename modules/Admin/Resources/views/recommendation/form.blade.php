<div class="form-body">
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/recommendation.recommendation-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('message_type', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/recommendation.recommendation-type') ])] + $messageType, null,['class'=>'select2me form-control', 'id' => 'message_type', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Message Type.']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/recommendation.recommendation-text') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::textarea('message_text', null, ['minlength'=>2,'maxlength'=>320,'class'=>'form-control', 'id'=>'message_text', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/recommendation.recommendation-text')]), 'data-rule-maxlength'=>'320', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/recommendation.recommendation-text')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/recommendation.recommendation-text')]) ])!!}
            <span class="help-block">Brief text for user to recommend Diet/Activity</span>
        </div>
    </div>
    <div><input type="hidden" id="status" value="1" name="status"></div>
</div>