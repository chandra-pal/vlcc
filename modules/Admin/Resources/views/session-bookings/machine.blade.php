
{!! Form::select('machine_id[]', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.machine') ])] + $centerMachineList, $machineId,['multiple'=>'multiple','class'=>'select2me form-control form-filter', 'id' => 'machine_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Machine.']) !!}
<span class="help-block help-block-error customer_error"></span>
<div id="set-machine-availability">
    {!! $machineResourceTime !!}

</div>