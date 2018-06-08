<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Country <span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::select('country_id', [''=>'Select Country'] + $countryList, null,['class'=>'select2me form-control country_id', 'id' => 'country_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Country.']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="state-drop-down">
                    @include('admin::locations.statedropdown')
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group" id="city-drop-down">
                    @include('admin::locations.citydropdown')
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Location Name <span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::text('location', null, ['minlength'=>2,'class'=>'form-control city_location', 'id'=>'location','data-rule-required'=>'true', 'data-msg-required'=>'Please enter Location Name.', 'data-rule-maxlength'=>'200', 'data-msg-maxlength'=>'Description may not have more than {0} letters.', 'maxlength'=>'200' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/locations.location')]) ])!!}
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Address Line 1</label>
                <div class="col-md-8">
                    {!! Form::text('address_1', null, ['minlength'=>2,'class'=>'form-control', 'id'=>'address_1', 'data-rule-maxlength'=>'200', 'data-msg-maxlength'=>'Description may not have more than {0} letters.', 'maxlength'=>'200' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/locations.address_line_1')]) ])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Address Line 2</label>
                <div class="col-md-8">
                    {!! Form::text('address_2', null, ['minlength'=>2,'class'=>'form-control', 'id'=>'address_2', 'data-rule-maxlength'=>'200', 'data-msg-maxlength'=>'Description may not have more than {0} letters.', 'maxlength'=>'200' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/locations.address_line_2')]) ])!!}
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Landmark</label>
                <div class="col-md-8">
                    {!! Form::text('landmark', null, ['minlength'=>2,'class'=>'form-control', 'id'=>'landmark', 'data-rule-maxlength'=>'200', 'data-msg-maxlength'=>'Description may not have more than {0} letters.', 'maxlength'=>'200' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/locations.landmark')]) ])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">ZIP/Pin Code</label>
                <div class="col-md-8">
                    {!! Form::text('zipcode', null, ['minlength'=>4,'class'=>'form-control', 'id'=>'zipcode', 'data-rule-maxlength'=>'10', 'data-msg-maxlength'=>'Description may not have more than {0} letters.', 'maxlength'=>'10' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/locations.zipcode')]) ])!!}
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Pin your Location</label>
                <div class="col-md-8">
                    <div id="gmap_geocoding_new" style="height: 300px;width: 100%"> </div>
                </div>
            </div>
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
