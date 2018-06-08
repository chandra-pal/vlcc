
{!! Form::select('package_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.select-package') ])] + $packageList, null,['class'=>'select2me form-control form-filter select-package', 'id' => 'package_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Package.']) !!}
<span class="help-block help-block-error customer_error"></span>
