<label class="col-md-5 control-label" style="text-align:left !important;">Machine<span class="required" aria-required="true">*</span> </label>
<div class="col-md-4" id="machine-listing-content">
    {!! Form::select('machine_id', [''=>'Select Machine'] + $machineList, null,['autocomplete' => 'off', 'class'=>'select2me form-control form-filter machine_id', 'id' => 'machine_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/machine-availability.machine')])]) !!}
</div>