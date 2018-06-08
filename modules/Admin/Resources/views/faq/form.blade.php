<div class="form-body">
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/faq.faq-category') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('faq_category_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/faq.faq-category') ])] + $categoryList, null,['class'=>'select2me form-control', 'id' => 'faq_category_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select FAQ Category.']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/faq.question') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-6">
            {!! Form::text('question', null, ['minlength'=>2,'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Question.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/faq.question')]) ])!!}
            <span class="help-block">Enter question. Eg. Is your site secure?</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/faq.answer') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-6">
            {!! Form::textarea('answer', null, ['minlength'=>2,'size' => '30x3','class'=>'form-control text-noresize', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Answer.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/faq.answer')]) ])!!}
            <span class="help-block">Answer to the question.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/faq.position') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('position', null, ['class'=>'form-control', 'data-rule-maxlength' => '10', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Display Order.']) !!}
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
