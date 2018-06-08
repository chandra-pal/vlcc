<div class="portlet box add-form-main medical-assessment" style="box-shadow: 0 2px 2px 0px rgba(0, 0, 0, 0);">
    <div class="caption">
        <h2>{!! trans('admin::controller/cpr.medical-assessment-form') !!}</h2>
        <hr>
    </div>
</div>

{!! Form::open(['route' => ['admin.cpr.store-medical-assessment'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal medical-assessment-form',  'id' => 'create-medical-assessment', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
<fieldset>
    <div class="portlet-body">
        <div class="panel-group accordion" id="accordion1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1" id='current_associated_mediacl_prob'> Current Associated Medical Problem </a>
                    </h4>
                </div>
                <div id="collapse_1" class="panel-collapse in">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="medical-table">
                            <thead>
                            </thead>
                            <tbody>
                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.medical-problem') !!}</td>
                                    <td colspan="3" id='medical_problem_cell'>
                                        @include('admin::cpr.medical-problem')
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.any-other') !!}</td>
                                    <td>
                                        <div id="other_div" style="display: none;">
                                            {!! Form::textarea('other', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control', 'id'=>'other',  'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.any-other')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.any-other')]) ])!!}
                                            <span id="other_error" style="color: #ff0000;"></span>

                                        </div>

                                    </td>
                                    <td> {!! trans('admin::controller/cpr.epilepsy-title') !!}</td>
                                    <td>
                                        <div id="epilepsy_div" style="display: none;">
                                            {!! Form::text('epilepsy', null, ['readonly' => 'true','minlength'=>2,'maxlength'=>30,'class'=>'form-control', 'id'=>'epilepsy', 'data-rule-maxlength'=>'30', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.epilepsy-date')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.epilepsy-date')]) ])!!}
                                            <span id="epilepsy_error" style="color: #ff0000;"></span>
                                        </div>

                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_2"> Biochemical Profile </a>
                    </h4>
                </div>
                <div id="collapse_2" class="panel-collapse collapse">
                    <div class="panel-body" style="overflow-y:auto;" id="biochemical_profile_div">

                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_3"> Other </a>
                    </h4>
                </div>
                <div id="collapse_3" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>

                            </thead>
                            <tbody>
                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.physical-finding') !!}</td>
                                    <td>
                                        {!! Form::textarea('physical_finding', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'physical_finding', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.physical-finding')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.physical-finding')]) ])!!}
                                    </td>

                                    <td> {!! trans('admin::controller/cpr.gyane-obstetrics') !!}</td>
                                    <td>
                                        {!! Form::textarea('gynae_obstetrics_history', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'gynae_obstetrics_history', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.gyane-obstetrics')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.gyane-obstetrics')]) ])!!}
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.systemic-examination') !!}</td>
                                    <td>
                                        {!! Form::textarea('systemic_examination', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'systemic_examination', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.systemic-examination')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.systemic-examination')]) ])!!}
                                    </td>

                                    <td> {!! trans('admin::controller/cpr.client-birth-weight') !!}</td>
                                    <td>

                                        {!! Form::text('clients_birth_weight', null, ['minlength'=>1,'maxlength'=>1,'class'=>'form-control min-one-required', 'id'=>'clients_birth_weight', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Birth Weight.']) !!}
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.sleep-pattern') !!}</td>
                                    <td colspan="3">
                                        <div class="radio-list">
                                            <div class="col-md-4 radio-container"><label class="radio-inline">{!! Form::radio('sleeping_pattern', 1, true,['id'=>'sp-1']) !!} Normal</label></div>
                                            <div class="col-md-4 radio-container"><label class="radio-inline">{!! Form::radio('sleeping_pattern', 2, null,['id'=>'sp-2']) !!} Disturbed</label></div>
                                            <div class="col-md-4 radio-container"><label class="radio-inline">{!! Form::radio('sleeping_pattern', 3, null,['id'=>'sp-3']) !!} Less Sleep</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.past-medical-history') !!}</td>
                                    <td>
                                        {!! Form::textarea('past_mediacl_history', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'past_mediacl_history', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.past-medical-history')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.past-medical-history')]) ])!!}
                                    </td>
                                    <td> {!! trans('admin::controller/cpr.family-history') !!}</td>
                                    <td>
                                        {!! Form::textarea('family_history_of_diabetes_obesity', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'family_history_of_diabetes_obesity', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.family-history')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.family-history')]) ])!!}
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td colspan="4"> Doctor's Comment : </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.detailed-history') !!}</td>
                                    <td>
                                        {!! Form::textarea('detailed_history', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'detailed_history', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.detailed-history')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.detailed-history')]) ])!!}
                                    </td>
                                    <td> {!! trans('admin::controller/cpr.treatment-history') !!}</td>
                                    <td>
                                        {!! Form::textarea('treatment_history', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'treatment_history', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.treatment-history')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.treatment-history')]) ])!!}
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.suggested-investigation') !!}</td>
                                    <td>
                                        {!! Form::textarea('suggested_investigation', null, ['rows'=>'2','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'suggested_investigation', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.suggested-investigation')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.suggested-investigation')]) ])!!}
                                    </td>
                                    <td> {!! trans('admin::controller/cpr.followup-date') !!}</td>
                                    <td>
                                        {!! Form::text('followup_date', null, ['readonly' => 'true','minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'followup_date', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.followup-date')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.followup-date')]) ])!!}
                                    </td>
                                </tr>

                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.doctor-name') !!}</td>
                                    <td>
                                        {!! Form::text('doctors_name', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control min-one-required', 'id'=>'doctors_name', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.doctor-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.doctor-name')]) ])!!}
                                    </td>

                                    <td> {!! trans('admin::controller/cpr.assessment-date') !!}</td>
                                    <td>
                                        {!! Form::text('assessment_date', null, ['readonly' => 'true','minlength'=>2,'maxlength'=>100,'class'=>'form-control review-date min-one-required', 'id'=>'assessment_date', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.assessment-date')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.assessment-date')]) ])!!}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <table class="table table-striped table-bordered table-hover" id="medical-table">
                        <thead>
                        </thead>
                        <tbody>
                            {{--- Commenting this code as this can be managed using ACL ---}}
                            {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
                            @if(!empty(Auth::guard('admin')->user()->hasAdd))
                            <tr role="row" class="heading">
                                <td>
                                    <button type="submit" class="btn green" style="margin-left: 5px; ">{!! trans('admin::messages.submit') !!}</button>
                                    <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                                </td>
                            </tr>
                            @endif
                            {{--- @endif ---}}
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</fieldset>
{!! Form::close() !!}