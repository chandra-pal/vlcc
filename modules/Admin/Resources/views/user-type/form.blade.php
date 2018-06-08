<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">User Type <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::text('name', null, ['minlength'=>2, 'maxlength'=>255 ,'class'=>'form-control', 'id'=>'name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.name')]), 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/usertype.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/usertype.name')]) ])!!}
                    <span class="help-block">Eg. Admin, Webmaster</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Description </label>
                <div class="col-md-9">
                    {!! Form::text('description', null, ['minlength'=>2, 'maxlength'=>255 ,'class'=>'form-control', 'id'=>'description', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/usertype.description')]), 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/usertype.description')]) ])!!}
                    <span class="help-block">Eg. Admin, Webmaster</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Priority <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::select('priority', [''=>'Select Priority'] + $priorityList, null,['class'=>'select2me form-control', 'id' => 'priority', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.priority')]) ]) !!}
                    <span class="help-block">Priority of role. 1 being the highest and 10 being the lowest.</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Status <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    <div class="radio-list">
                        <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                        <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
