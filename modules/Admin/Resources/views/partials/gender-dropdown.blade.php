<?php $routeName = Route::current()->getName();?>
@if(((Auth::guard('admin')->user()->userType->id == "4" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "7" || Auth::guard('admin')->user()->userType->id == "8" || Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName === 'admin.session-bookings.index') || ($routeName === 'admin.view-todays-sessions.list')))
<?php $class = "col-md-3";?>
@else
<?php $class = "";?>
@endif
<div class="form-group {{$class}}">
    <div>
        @if(isset($memGender))
       <select id="customer_gender" class="select2me form-control" name="customer_gender" autocomplete="off">
            <option value="">Select Gender</option>
            <option value="1">Male</option>
            <option value="2">Female</option>
        </select>
        <span class="help-block help-block-error customer_error"></span>
         @endif
    </div>
</div>

