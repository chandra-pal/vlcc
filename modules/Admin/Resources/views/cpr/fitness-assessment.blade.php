<div class="portlet box add-form-main fitness-assessment" style="box-shadow: 0 2px 2px 0px rgba(0, 0, 0, 0);">
    <div class="caption">
        <h2>{!! trans('admin::controller/cpr.fitness-assessment-form') !!}</h2>
        <hr>
    </div>

    <div class="portlet-body form">

        {!! Form::open(['route' => ['admin.cpr.store-fitness-assessment'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal fitness-assessment-form',  'id' => 'create-fitness-assessment', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        <fieldset>
            <table class="table table-striped table-bordered table-hover" id="fitness-table">
                <thead>
                </thead>
                <tbody>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.static-posture') !!}</td>
                        <td>
                            {!! Form::text('static_posture', null, ['minlength'=>1,'maxlength'=>1,'class'=>'form-control min-one-required', 'id'=>'static_posture', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}

                            <span class="help-block"> eg : Test Score 4 / 3 / 2 / 1</span>
                        </td>

                        <td> {!! trans('admin::controller/cpr.sit-and-reach-test') !!}</td>
                        <td>
                            {!! Form::text('sit_and_reach_test', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'sit_and_reach_test', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}
                        </td>
                    </tr>

                    <tr role="row" class="heading">
                        <td colspan="4"> <b>Shoulder Flexibility Test : </b></td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.shoulder-right') !!} (in cm)</td>
                        <td>
                            {!! Form::text('shoulder_flexibility_right', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'shoulder_flexibility_right', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}
                        </td>
                        <td> {!! trans('admin::controller/cpr.shoulder-left') !!} (in cm)</td>
                        <td>
                            {!! Form::text('shoulder_flexibility_left', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'shoulder_flexibility_left', 'data-rule-number' => '10',  'data-msg-number'=>'Please enter numbers only.' ]) !!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.pulse') !!}</td>
                        <td>
                            {!! Form::text('pulse', null, ['minlength'=>1,'maxlength'=>3,'class'=>'form-control min-one-required', 'id'=>'pulse', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter numbers only.' ]) !!}
                            <span class="help-block"> eg : 120</span>
                        </td>
                        <td> {!! trans('admin::controller/cpr.back-problem-test') !!}</td>
                        <td>
                            <div class="radio-list">
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('back_problem_test', '1', true) !!} Positive</label></span>
                                <span class="radio-container"><label class="radio-inline">{!! Form::radio('back_problem_test', '0', null) !!} Negative</label></span>
                                </br>
                                <span id='back-problem-error' class="help-block" style="color: #F3565D !important;"></span>
                            </div>
                        </td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.current-activity-pattern') !!}</td>
                        <td colspan="3">
                            {!! Form::text('current_activity_pattern', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'current_activity_pattern', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.current-activity-pattern')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.current-activity-pattern')]) ])!!}
                        </td>

                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.current-activity-type') !!}</td>
                        <td>
                            {!! Form::text('current_activity_type', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'current_activity_type', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.current-activity-type')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.current-activity-type')]) ])!!}
                        </td>
                        <td> {!! trans('admin::controller/cpr.current-activity-frequency') !!}</td>
                        <td>
                            {!! Form::text('current_activity_frequency', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'current_activity_frequency', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.current-activity-frequency')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.current-activity-frequency')]) ])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.current-activity-duration') !!}</td>
                        <td>
                            {!! Form::text('current_activity_duration', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'current_activity_duration', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.current-activity-duration')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.current-activity-duration')]) ])!!}
                        </td>
                        <td> Remark / Contraindications</td>
                        <td>
                            {!! Form::textarea('remark', null, ['rows'=>'2','minlength'=>2,'maxlength'=>320,'class'=>'form-control min-one-required', 'id'=>'remark', 'data-rule-maxlength'=>'320', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.remarks')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.remarks')]) ])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.home-kit-care') !!}</td>
                        <td>
                            <div class="radio-list">
                                <span class="radio-container1"><label class="radio-inline">{!! Form::radio('home_care_kit', '0', true) !!} {!! trans('admin::messages.no') !!}</label></span>
                                <span class="radio-container1"><label class="radio-inline">{!! Form::radio('home_care_kit', '1', null) !!} {!! trans('admin::messages.yes') !!}</label></span>
                                </br>
                                <span id='home-care-kit-error' class="help-block" style="color: #F3565D !important;"></span>
                            </div>
                        </td>
                        <td> {!! trans('admin::controller/cpr.physiotherapist-name') !!}</td>
                        <td>
                            {!! Form::text('physiotherapist_name', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'physiotherapist_name',  'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.physiotherapist-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.physiotherapist-name')]) ])!!}
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

