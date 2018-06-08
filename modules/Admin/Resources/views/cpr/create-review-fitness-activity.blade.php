<div class="portlet box blue add-form-main review-record-form stop-open">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.review')]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.cpr.store-review-fitness-activity-records'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal review-fitness-activity-records-form',  'id' => 'create-review-fitness-activity-records', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        </fieldset>
        <div class="portlet light col-lg-12">
            <div class="">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <input id="data-search" type="search" class="form-control" placeholder="Search">
                    </div>

                    <table class="table table-striped table-bordered table-hover" id="diet-plan-table">
                        <thead>

                        </thead>
                        <tbody>
                            <tr role="row" class="heading">
                                <td> Review Date</td>
                                <td>
                                    {!! Form::text('review_date', \Carbon\Carbon::now()->format('d-m-Y'), ['readonly' => 'true', 'class'=>'form-control skip review-date', 'id'=>'recorded_date'])!!}
                                </td>
                                <td> Static Posture Score </td>
                                <td>
                                    {!! Form::text('static_posture_score', null, ['minlength'=>1,'maxlength'=>1,'class'=>'form-control min-one-required', 'id'=>'static_posture_score', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.static-posture-score')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.static-posture-score')]) ])!!}
                                </td>
                            </tr>

                            <tr role="row" class="heading">
                                <td> Sit & Reach Test  </td>
                                <td>
                                    {!! Form::text('sit_and_reach_test', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'sit_and_reach_test', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.sit-and-reach-test')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.sit-and-reach-test')]) ])!!}
                                </td>

                                <td> Pulse</td>
                                <td>
                                    {!! Form::text('pulse', null, ['maxlength'=>5,'class'=>'form-control', 'id'=>'pulse', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Pulse.'])!!}
                                    <span class="help-block"> eg : 120</span>
                                </td>

                            </tr>
                            <tr role="row" class="heading">
                                <td colspan="4">
                                    Shoulder Flexibility Test :
                                </td>
                            </tr>
                            <tr role="row" class="heading">
                                <td> Right Shoulder </td>
                                <td>
                                    {!! Form::text('right_shoulder_flexibility', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'right_shoulder_flexibility', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.shoulder-right')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.shoulder-right')]) ])!!}
                                </td>
                                <td> Left Shoulder </td>
                                <td>
                                    {!! Form::text('left_shoulder_flexibility', null, ['minlength'=>1,'maxlength'=>10,'class'=>'form-control min-one-required', 'id'=>'left_shoulder_flexibility', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.shoulder-left')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.shoulder-left')]) ])!!}
                                </td>
                            </tr>


                            <tr role="row" class="heading">
                                <td> SLR </td>
                                <td colspan="3">
                                    {!! Form::text('slr', null, ['minlength'=>1,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'slr', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.slr')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.slr')]) ])!!}
                                </td>

                            </tr>

                            <tr role="row" class="heading">
                                <td> Activity Code </td>
                                <td>
                                    {!! Form::text('specific_activity_advice', null, ['minlength'=>1,'maxlength'=>2,'class'=>'form-control min-one-required', 'id'=>'specific_activity_advice', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.activity-code')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.activity-code')]) ])!!}
                                    <span class="help-block">( Please refer below Activity Code table. )</span>
                                    <span class="help-block"> eg :A</span>
                                </td>

                                <td> Activity Duration (in minutes)</td>
                                <td>
                                    {!! Form::text('specific_activity_duration', null, ['minlength'=>1,'maxlength'=>3,'class'=>'form-control min-one-required', 'id'=>'specific_activity_duration', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Activity Duration.'])!!}
                                    <span class="help-block">Eg: 20</span>


                                </td>

                            </tr>

                            <tr role="row" class="heading">
                                <td> Physiotherapist Name </td>
                                <td>
                                    {!! Form::text('physiotherapist_name', null, ['minlength'=>1,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'physiotherapist_name', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.physiotherapist-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.physiotherapist-name')]) ])!!}
                                </td>

                                <td> Precautions & Contraindications</td>
                                <td>
                                    {!! Form::textarea('precautions_and_contraindications', null, ['rows'=>'2','minlength'=>1,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'precautions_and_contraindications', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.precautions-contraindications')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.precautions-contraindications')]) ])!!}
                                </td>

                            </tr>
                            <tr role="row" class="heading">
                                <td colspan="4">
                                    <button type="submit" class="btn green" style="margin-left: 5px; ">{!! trans('admin::messages.submit') !!}</button>
                                    <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>