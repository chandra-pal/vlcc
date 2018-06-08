<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Center Name<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('center_name', null, ['minlength'=>5,'maxlength'=>60,'class'=>'form-control', 'id'=>'center_name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.center_name')])])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Country<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('country_id', [''=>'Select Country'] + $countryList, null,['class'=>'select2me form-control country_id', 'id' => 'country_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Country.']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">State<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            @include('admin::center.statedropdown')
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">City<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            @include('admin::center.citydropdown')
        </div>
    </div>
    <!--<div class="form-group">
        <label class="col-md-3 control-label">Address<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('address', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'address', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.address')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/center.address')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/center.address')]) ])!!}
        </div>
    </div>-->
    <div class="form-group">
        <label class="col-md-3 control-label">Address<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('address', null, ['minlength'=>5,'maxlength'=>255,'class'=>'form-control', 'id'=>'address', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.address')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Area<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('area', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'area', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.area')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/center.area')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/center.area')]) ])!!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">ZIP/Pin Code<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('pincode', null, ['minlength'=>2,'maxlength'=>10,'class'=>'form-control', 'id'=>'pincode', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.pincode')]) ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Pin your Location<span class="required" aria-required="true">*</span></label>
        <div class="col-md-9" id="add-map-container">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Latitude</label>
                <div class="col-md-8">
                    {!! Form::text('latitude', null, ['class'=>'form-control', 'id'=>'latitude', 'readonly' => 'true' ])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Longitude</label>
                <div class="col-md-8">
                    {!! Form::text('longitude', null, ['class'=>'form-control', 'id'=>'longitude', 'readonly' => 'true' ])!!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Phone Number<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('phone_number', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'phone_number', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.phone')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/center.phone')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/center.phone')]) ])!!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Status </label>
                <div class="col-md-8">
                    <div class="radio-list">
                        <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                        <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
