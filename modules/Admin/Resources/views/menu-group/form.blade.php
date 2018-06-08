<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/menu-group.name') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('name', null, ['minlength'=>2,'maxlength'=>150,'class'=>'form-control', 'id'=>'name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/menu-group.name')]), 'data-rule-maxlength'=>'150', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/menu-group.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/menu-group.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/menu-group.position') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('position', null, ['class'=>'form-control', 'id'=>'position', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/menu-group.position')]) ]) !!}
        </div>
    </div>
</div>