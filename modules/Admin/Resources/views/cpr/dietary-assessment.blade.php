
<div class="portlet box add-form-main dietary-assessment" style="box-shadow: 0 2px 2px 0px rgba(0, 0, 0, 0);">
    <div class="caption">
        <h2>{!! trans('admin::controller/cpr.dietary-assessment-form') !!}</h2>
        <hr>
    </div>

    <div class="portlet-body form">

        {!! Form::open(['route' => ['admin.cpr.store-dietary-assessment'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal dietary-assessment-form',  'id' => 'create-dietary-assessment', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        <fieldset>
            <table class="table table-striped table-bordered table-hover" id="dietary-table">
                <thead>
                </thead>
                <tbody>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.food-allergy') !!} (if any)</td>
                        <td>
                            {!! Form::text('food_allergy', null, ['minlength'=>2,'maxlength'=>256,'class'=>'form-control min-one-required', 'id'=>'food_allergy', 'data-rule-maxlength'=>'256', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.food-allergy')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.food-allergy')]), 'requiredFromGroup'=>true ])!!}
                            <span class="help-block"> eg : No</span>
                        </td>

                        <td> Frequency of Eating Out / Week (in days)</td>
                        <td>
                            {!! Form::text('eat_out_per_week', null, ['minlength'=>1,'maxlength'=>2,'class'=>'form-control min-one-required', 'id'=>'eat_out_per_week', 'data-rule-number' => '10',  'data-msg-number'=>'Please enter numbers only.' ]) !!}
                            <span class="help-block"> eg : 5</span>
                        </td>
                    </tr>



                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.fasting') !!} (in days)</td>
                        <td colspan="3">
                            {!! Form::text('fasting', null, ['minlength'=>1,'maxlength'=>1,'class'=>'form-control min-one-required', 'id'=>'fasting', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.']) !!}
                            <span class="help-block"> eg : 2</span>
                        </td>
                    </tr>


                    <tr role="row" class="heading">
                        <td> Number of Meals Consumed / Day</td>
                        <td>
                            {!! Form::text('meals_per_day', null, ['minlength'=>1,'maxlength'=>2,'class'=>'form-control min-one-required', 'id'=>'meals_per_day', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.']) !!}
                            <span class="help-block"> eg : 3</span>
                        </td>
                        <td> {!! trans('admin::controller/cpr.food-habbit') !!}</td>
                        <td>
                            <div class="radio-list">
                                @foreach($food_habbit_types as $k => $v)
                                @if($k==1)
                                <?php //$val = "true"; ?>
                                @else
                                <?php //$val = null; ?>
                                @endif
                                <div class="col-md-6">
                                    <span class="radio-container"><label class="radio-inline">{!! Form::radio('food_habbit', $k) !!} {!! $v !!}</label></span>
                                </div>
                                @endforeach


                            </div>
                            </br><span id='food_habbit-error' class="help-block" style="color: #F3565D !important;"></span>
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.smoking') !!}</td>
                        <td>
                            <div class="radio-list" >
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('smoking', '0', true) !!} {!! trans('admin::messages.no') !!}</label></span>
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('smoking', '1', null) !!} {!! trans('admin::messages.yes') !!}</label></span>
                                </br>
                                <span id='smoking-error' class="help-block" style="color: #F3565D !important;"></span>
                            </div>
                        </td>
                        <td> {!! trans('admin::controller/cpr.smoking-frequency') !!}</td>
                        <td>
                            <div class="radio-list smoking_frequency_div" style="display: none;">
                                <div class="col-md-6"><span class="radio-container-smoking-frequency"><label class="radio-inline" style="">{!! Form::radio('smoking_frequency', '1') !!} {!! trans('admin::controller/cpr.everyday') !!}</label></span></div>
                                <div class="col-md-6"><span class="radio-container-smoking-frequency"><label class="radio-inline">{!! Form::radio('smoking_frequency', '2') !!} {!! trans('admin::controller/cpr.weekly') !!}</label></span></div>
                                <div class="col-md-6"><span class="radio-container-smoking-frequency"><label class="radio-inline">{!! Form::radio('smoking_frequency', '3') !!} {!! trans('admin::controller/cpr.occasionally') !!}</label></span></div>
                                </br>
                                <span id='smoking_frequency-error' class="help-block" style="color: #F3565D !important;"></span>
                            </div>
                        </td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.alcohol') !!}</td>
                        <td>
                            <div class="radio-list">
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('alcohol', '0', true) !!} {!! trans('admin::messages.no') !!}</label></span>
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('alcohol', '1', null) !!} {!! trans('admin::messages.yes') !!}</label></span>
                                </br>
                                <span id='alcohol-error' class="help-block" style="color: #F3565D !important;"></span>
                            </div>
                        </td>
                        <td> {!! trans('admin::controller/cpr.alcohol-frequency') !!}</td>
                        <td>
                            <div class="radio-list alcohol_frequency_div" style="display: none;">
                                <div class="col-md-6"><span class="radio-container-alcohol-frequency"><label class="radio-inline">{!! Form::radio('alcohol_frequency', '1') !!} {!! trans('admin::controller/cpr.everyday') !!}</label></span></div>
                                <div class="col-md-6"><span class="radio-container-alcohol-frequency"><label class="radio-inline">{!! Form::radio('alcohol_frequency', '2') !!} {!! trans('admin::controller/cpr.weekly') !!}</label></span></div>
                                <div class="col-md-6"><span class="radio-container-alcohol-frequency"><label class="radio-inline">{!! Form::radio('alcohol_frequency', '3') !!} {!! trans('admin::controller/cpr.occasionally') !!}</label></span></div>
                                </br>


                            </div>
                            </br><span id='alcohol_frequency-error' class="help-block" style="color: #F3565D !important;"></span>
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.diet-total-calories') !!}</td>
                        <td>
                            {!! Form::text('diet_total_calories', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control min-one-required', 'id'=>'diet_total_calories', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}
                            <span class="help-block"> eg : 1400</span>
                        </td>
                        <td> {!! trans('admin::controller/cpr.diet-cho') !!}</td>
                        <td>
                            {!! Form::text('diet_cho', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control min-one-required', 'id'=>'diet_cho', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}
                            <span class="help-block"> eg : 50</span>
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.diet-protein') !!}</td>
                        <td>
                            {!! Form::text('diet_protein', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control min-one-required', 'id'=>'diet_protein', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.']) !!}
                            <span class="help-block"> eg : 100</span>
                        </td>
                        <td> {!! trans('admin::controller/cpr.diet-fat') !!}</td>
                        <td>
                            {!! Form::text('diet_fat', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control min-one-required', 'id'=>'diet_fat', 'data-rule-number' => '10',  'data-msg-number'=>'Please enter numbers only.' ]) !!}
                            <span class="help-block"> eg : 40</span>
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.remarks') !!}</td>
                        <td>
                            {!! Form::textarea('remark', null, ['rows'=>'2','minlength'=>2,'maxlength'=>320,'class'=>'form-control min-one-required', 'id'=>'remark', 'data-rule-maxlength'=>'320', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.remarks')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.remarks')]) ])!!}
                        </td>
                        <td> {!! trans('admin::controller/cpr.wellness-counsellor-name') !!}</td>
                        <td>
                            {!! Form::text('wellness_counsellor_name', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'wellness_counsellor_name', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.wellness-counsellor-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.wellness-counsellor-name')]) ])!!}
                        </td>
                    </tr>

                    {{--- Commenting this code as this can be managed using ACL ---}}
                    {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
                    @if(!empty(Auth::guard('admin')->user()->hasAdd))
                    <tr role="row" class="heading">
                        <td colspan="4">
                            <button type="submit" class="btn green" style="margin-left: 5px; ">{!! trans('admin::messages.submit') !!}</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                        </td>
                    </tr>
                    @endif
                    {{--- @endif ---}}

                </tbody>
            </table>
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>




