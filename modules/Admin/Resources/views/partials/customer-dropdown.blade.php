<?php $routeName = Route::current()->getName();?>
@if((Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName !== 'admin.session-bookings.index' || $routeName !== 'admin.view-todays-sessions.list'))
<?php $class = "col-md-6";?>
@else
<?php $class = "";?>
@endif

@if(((Auth::guard('admin')->user()->userType->id == "4" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "7" || Auth::guard('admin')->user()->userType->id == "8" || Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName === 'admin.session-bookings.index') || ($routeName === 'admin.view-todays-sessions.list')))
<?php $class = "col-md-4";?>

@else
<?php $class = "col-md-6";?>
@endif

    <div class="form-group <?php  if(isset($class) && !empty($class)) echo $class; ?>">
    <div>
        <?php $selectedMember = Session::get('member_id') ? Session::get('member_id') : null; ?>
        @if(isset($membersList))
        {!! Form::select('message_type', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member.member') ])] + $membersList, $selectedMember,['class'=>'select2me form-control', 'id' => 'customer_select', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Customer.']) !!}
        <span class="help-block help-block-error customer_error"></span>
        @endif
    </div>
</div>
