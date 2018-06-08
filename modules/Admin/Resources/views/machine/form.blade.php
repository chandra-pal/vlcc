<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Machine Type<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('machine_type_id', [''=>'Select Machine Type'] + $machineTypesList, null,['autocomplete' => 'off','class'=>'select2me form-control', 'id' => 'machine_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/machine.machine-type')])]) !!}
            <span class="help-block">Select machine type.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Machine Name <span class="required" aria-required="true">*</span></label>
        <span id="sel-machine-type"></span>
        <div class="col-md-4">
            {!! Form::text('name', $machineName1, ['maxlength'=>'200', 'id'=>'machine-name', 'class'=>'form-control','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/machine.name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/machine.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Centers <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('center_id[]',[''=>'Select Center'] +$centerList, $selectedCenters,['autocomplete' => 'off','multiple'=>'multiple','class'=>'select2me form-control', 'id' => 'center_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/machine.center')])]) !!}
            <span class="help-block">Assign machines to centers.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Description</label>
        <div class="col-md-4">
            {!! Form::textarea('description', null, ['minlength'=>2,'size' => '30x3','class'=>'form-control', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/machine.machine-description')]) ])!!}
            <span class="help-block">Machine Description.</span>
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