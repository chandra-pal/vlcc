<label class="col-md-4 control-label">City <span class="required" aria-required="true">*</span></label>
<div class="col-md-8" id='city-listing-content'>
    {!! Form::select('city_id', [''=> trans('admin::messages.select-name',['name'=>trans('admin::controller/locations.city')]) ] + $cityList, null,['class'=>'select2me form-control form-filter city_id', 'id' => 'city_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select City.']) !!}
</div>
