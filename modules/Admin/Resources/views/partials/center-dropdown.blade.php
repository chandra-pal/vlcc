<?php $routeName = Route::current()->getName();?>
@if((Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName !== 'admin.session-bookings.index' || $routeName !== 'admin.view-todays-sessions.list'))
<?php $class = "col-md-6";?>
@else
<?php $class="";?>
@endif

@if(((Auth::guard('admin')->user()->userType->id == "4" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "7" || Auth::guard('admin')->user()->userType->id == "8" || Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "11") && ($routeName === 'admin.session-bookings.index') || ($routeName === 'admin.view-todays-sessions.list')))
<?php $class = "col-md-5";?>
@else
<?php $class = "col-md-6";?>
@endif

<div class="form-group <?php if(isset($class) && !empty($class)) echo $class; ?>">
    <div class="list_centers">
        <?php $selectedCenter = Session::get('center_id') ? Session::get('center_id') : null; ?>
        @if(isset($centersList))
            @if(count($centersList) == 1)
                {!! Form::select('center_id',$centersList, $selectedCenter,['class'=>'select2me form-control', 'id' => 'center_select', 'autocomplete'=>'off', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
            @else
                {!! Form::select('center_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member.center') ])] + $centersList, $selectedCenter,['class'=>'select2me form-control', 'id' => 'center_select', 'autocomplete'=>'off', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
                <span class="help-block help-block-error"></span>
            @endif    
        @endif
    </div>
    <span style="color: red; display: none;" id="select-center-error">Please Select Center</span>
</div>
