<?php $routeName = Route::current()->getName();?>
@if(((Auth::guard('admin')->user()->userType->id == "4" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "7" || Auth::guard('admin')->user()->userType->id == "8" || Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName === 'admin.session-bookings.index') || ($routeName === 'admin.view-todays-sessions.list')))
<?php $class = "col-md-3";?>
@else
<?php $class = "";?>
@endif
<div class="form-group {{$class}}">
    <div>
        @if(isset($pacServiceCat))
       <select id="customer_service_cat" class="select2me form-control" name="customer_service_cat" autocomplete="off">
            <option value="">Select Service Category</option>
            <option value="100000001">Slimming</option>
            <option value="100000002">Beauty</option>
        </select>
        <span class="help-block help-block-error customer_error"></span>
         @endif
    </div>
</div>

