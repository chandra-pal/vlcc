{!! Form::open(['route' => ['admin.cpr.store'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal cpr-form',  'id' => 'create-cpr', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
<fieldset>
    <div class="portlet light col-lg-12">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa icon-user font-blue-sharp"></i>
                <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/cpr.personal-info') !!}</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    <input id="data-search" type="search" class="form-control" placeholder="Search">
                </div>
                <table class="table table-striped table-bordered table-hover" id="personal-info-table">
                    <tbody>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.client-id') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td colspan="2">
                                {!! Form::text('client_id', null, ['class'=>'form-control', 'id'=>'client_id'])!!}
                            </td>

                            <td>{!! trans('admin::controller/cpr.package-no') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td colspan="2">
                                {!! Form::text('member_package_number', null, ['class'=>'form-control', 'id'=>'member_package_number'])!!}
                                {!! Form::hidden('package_no', null, ['class'=>'form-control', 'id'=>'package_no'])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.client-first-name') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('first_name', null, ['class'=>'form-control', 'id'=>'first_name','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.client-first-name')])])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.client-last-name') !!}</td>
                            <td>
                                {!! Form::text('last_name', null, ['class'=>'form-control', 'id'=>'last_name','maxlength'=>'50'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.gender') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                <div class="radio-list" >
                                    <label class="radio-inline">
                                        {!! Form::radio('gender', '1', null) !!} {!! trans('admin::controller/cpr.male') !!}
                                    </label>
                                    <label class="radio-inline">
                                        {!! Form::radio('gender', '2', null) !!} {!! trans('admin::controller/cpr.female') !!}
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.dob') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('dob', null, ['class'=>'form-control datepicker', 'id'=>'dob','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.dob')])])!!}
                            </td>


                            <td>{!! trans('admin::controller/cpr.age') !!}</td>
                            <td>
                                {!! Form::text('age', null, ['class'=>'form-control', 'id'=>'age'])!!}
                                <span id='age-error'></span>
                            </td>

                            <td>{!! trans('admin::controller/cpr.height') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('height', null, ['class'=>'form-control', 'id'=>'height','data-rule-required'=>'true', 'data-rule-number'=>'true','data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.height')])])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.client-waist') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('waist', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control', 'id'=>'waist','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.client-waist')]),'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.whtr') !!}</td>
                            <td>
                                {!! Form::text('whtr', null, ['class'=>'form-control', 'id'=>'whtr'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.weight') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('weight', null, ['class'=>'form-control', 'id'=>'weight','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.weight')])])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.bmi') !!}</td>
                            <td>
                                {!! Form::text('bmi', null, ['class'=>'form-control', 'id'=>'bmi'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.address') !!}</td>
                            <td>
                                {!! Form::textarea('address', null, ['rows'=>'3','class'=>'form-control', 'id'=>'address', 'maxlength'=>'255','data-rule-maxlength'=>'255'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.profession') !!}</td>
                            <td>
                                {!! Form::text('profession', null, ['class'=>'form-control', 'id'=>'profession'])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.alternate-phone-number') !!}</td>
                            <td>
                                {!! Form::text('alternate_phone_number', null, ['class'=>'form-control', 'id'=>'alternate_phone_number', 'maxlength'=>'10','data-rule-number' => 'true', 'data-msg-number'=>'Please enter valid Alternate Phone Number.'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.mobile') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('mobile', null, ['class'=>'form-control', 'id'=>'mobile'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.email') !!}</td>
                            <td>
                                {!! Form::text('email', null, ['class'=>'form-control', 'id'=>'email','maxlength'=>'50','data-rule-maxlength'=>'50','data-rule-email' => 'true',])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.existing-medical-problem') !!}</td>
                            <td>
                                {!! Form::text('existing_medical_problem', null, ['class'=>'form-control', 'id'=>'existing_medical_problem'])!!}
                            </td>

                            <td>{!! trans('admin::controller/cpr.category-code') !!}</td>
                            <td>
                                {!! Form::text('category_code', null, ['class'=>'form-control', 'id'=>'category_code'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.services-to-be-avoided') !!}</td>
                            <td>
                                {!! Form::text('services_to_be_avoided', null, ['class'=>'form-control', 'id'=>'services_to_be_avoided'])!!}
                            </td>
                        </tr>
                        <tr role="row">

                            <td>{!! trans('admin::controller/cpr.family-physician-name') !!}</td>
                            <td>
                                {!! Form::text('family_physician_name', null, ['class'=>'form-control', 'id'=>'family_physician_name'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.family-physician-number') !!}</td>
                            <td>
                                {!! Form::text('family_physician_number', null, ['class'=>'form-control', 'id'=>'family_physician_number','maxlength'=>'10','data-rule-number' => 'true', 'data-msg-number'=>'Please enter valid Family Physician Number.'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.programme-needed') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('programme_needed', null, ['class'=>'form-control', 'id'=>'programme_needed','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.programme-needed')])])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.programme-booked') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                            <td>
                                {!! Form::text('programme_booked', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control', 'id'=>'programme_booked','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.programme-booked')]),'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.need-gap') !!}</td>
                            <td>
                                {!! Form::text('need_gap', null, ['class'=>'form-control', 'id'=>'need_gap'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.therapies') !!}</td>
                            <td>
                                {!! Form::textarea('therapies', null, ['rows'=>'3','class'=>'form-control', 'id'=>'therapies'])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.programme-re-booked') !!}</td>
                            <td>
                                {!! Form::textarea('programme_re_booked', null, ['rows'=>'3','class'=>'form-control', 'id'=>'programme_re_booked'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.conversion') !!}</td>
                            <td>
                                {!! Form::textarea('conversion', null, ['rows'=>'3','class'=>'form-control', 'id'=>'conversion'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.remarks') !!}</td>
                            <td>
                                {!! Form::textarea('remarks', null, ['rows'=>'3','class'=>'form-control', 'id'=>'remarks'])!!}
                            </td>
                        </tr>
                        <tr role="row">
                            <td>{!! trans('admin::controller/cpr.programme-booked-rs') !!}</td>
                            <td>
                                {!! Form::text('programme_booked_rs', null, ['class'=>'form-control', 'id'=>'programme_booked_rs'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.programme-booked-by') !!}</td>
                            <td>
                                {!! Form::text('programme_booked_by', null, ['class'=>'form-control', 'id'=>'programme_booked_by','maxlength'=>'50', 'data-rule-charachter'=>'true','data-rule-maxlength'=>'50'])!!}
                            </td>
                            <td>{!! trans('admin::controller/cpr.payment-made') !!}</td>
                            <td>
                                {!! Form::text('payment_made', null, ['class'=>'form-control', 'id'=>'payment_made'])!!}
                            </td>
                        </tr>
                        <!--
    
                                       <td>{!! trans('admin::controller/cpr.booked-by') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                                        <td>
                                            {!! Form::text('booked_by', null, ['class'=>'form-control', 'id'=>'booked_by'])!!}
                                        </div>
                                    </div>
                                </div>-->
                        <!--        <div class="col-md-12">
                                    <div class="panel-heading text-center"><h3>{!! trans('admin::controller/cpr.amount-paid-by-client') !!}</h3></div>
                                    <div class="table-container">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr role="row" class="heading">
                                                    <th>{!! trans('admin::controller/cpr.date-of-payment') !!}</th>
                                                    <th>{!! trans('admin::controller/cpr.bill-no') !!}</th>
                                                    <th>{!! trans('admin::controller/cpr.amount') !!}</th>
                                                    <th>{!! trans('admin::controller/cpr.mode-of-payment') !!}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{!! Form::text('date_of_payment', null, ['class'=>'form-control datepicker', 'id'=>'date_of_payment'])!!}</td>
                                                    <td>{!! Form::text('bill_no', null, ['class'=>'form-control', 'id'=>'bill_no'])!!}</td>
                                                    <td>{!! Form::text('amount', null, ['class'=>'form-control', 'id'=>'amount'])!!}</td>
                                                    <td>{!! Form::text('mode_of_payment', null, ['class'=>'form-control', 'id'=>'mode_of_payment'])!!}</td>
                                                </tr>
    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>-->
                        {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
                        @if(!empty(Auth::guard('admin')->user()->hasAdd))
                        <tr role="row">
                            <td colspan="6">
                                <button type="submit" class="btn green">{!! trans('admin::messages.submit') !!}</button>
                                <button type="button" class="btn default" onclick="window.location.href = '{{ route('admin.view-todays-sessions.list') }}'">{!! trans('admin::messages.cancel') !!}</button>
                            </td>
                        </tr>
                        @endif
                        {{--- @endif ---}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</fieldset>
{!! Form::close() !!}
