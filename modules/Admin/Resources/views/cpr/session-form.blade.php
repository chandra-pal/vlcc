<div class="portlet light col-lg-12">
    <div id="ajax-response-text-session" class="hidden">
        <div class="alert alert-warning">
            <i class="fa-lg fa fa-warning"></i>  Session record for this session is already present
        </div>
    </div>
    <div class="">
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="session-record-table-form">
                <thead>
                    <tr role="row" class="heading">
                        <th>BP</th>
                        <th>Date</th>
                        <th>Before Weight (kg)</th>
                        <th>After Weight (kg)</th>
                        <th>A Code</th>
                        <th>Diet & Activity Deviation, if any</th>
                        <th>Therapist</th>
                    </tr>
                </thead>
                <tbody>
                    <tr role="row">
                        <td>
                            {!! Form::text('bp', null, ['class'=>'form-control',  'id'=>'bp', 'data-rule-required'=>$required, 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.bp')])])!!}
                            <span class="help-block"> eg: 120/80 </span>
                        </td>
                        <td>
                            {!! Form::text('recorded_date', null, ['class'=>'form-control session-date skip-date',  'id'=>'recorded_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.recorded-date')])])!!}
                        </td>
                        <td>
                            {!! Form::text('before_weight', null, ['class'=>'form-control',  'id'=>'before_weight', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.before_weight')])])!!}
                        </td>
                        <td>
                            {!! Form::text('after_weight', null, ['class'=>'form-control',  'id'=>'after_weight', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.after_weight')])])!!}
                        </td>
                        <td>
                            {!! Form::text('a_code', null, ['class'=>'form-control',  'id'=>'a_code', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.a_code')])])!!}

                        </td>
                        <td>
                            {!! Form::text('diet_and_activity_deviation', null, ['class'=>'form-control',  'id'=>'diet_and_activity_deviation'])!!}
                        </td>
                        <td>
                            {!! Form::select('therapist_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/cpr.select-therapist') ])] + $therapistList, null, ['class'=>'select2me form-control form-filter select-therapist', 'id' => 'therapist_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Therapist.']) !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>