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
                        <td> Session Date<span class="required" aria-required="true" style="color: #ff0000;">*</span></td>
                        <td>
                                {!! Form::select('recorded_date', [''=>'Select Session Date'] +$sessionDateData, null,['autocomplete' => 'off', 'class'=>'select2me form-control', 'id' => 'recorded_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.recorded-date')])]) !!}
                        </td>

                        <td> Neck (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('neck', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'neck', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Neck Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.neck')])])!!}
                        </td>

                        <td> Chest (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/><small>10 cm below arm pit</small> </td>
                        <td>
                            {!! Form::text('chest', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'chest', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Chest Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.chest')])])!!}
                        </td>

                        <td>Left Arm (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span> <br/><small>MUAC (mid upper arm circumference) of left arm</small></td>
                        <td>
                            {!! Form::text('arms', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'arms', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Left Arms Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.left-arms')])])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td>Right Arm (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span> <br/><small>MUAC (mid upper arm circumference) of right arm</small></td>
                        <td>
                            {!! Form::text('arm_right', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'arm_right', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Right Arms Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.right-arms')])])!!}
                        </td>
                        <td> Tummy (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/><small>region of maximum grith</small> </td>
                        <td>
                            {!! Form::text('tummy', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'tummy', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Tummy Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.tummy')])])!!}
                        </td>

                        <td> Waist (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/> <small>high point of right iliac crest</small> </td>
                        <td>
                            {!! Form::text('waist', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'waist', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Waist Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.waist')])])!!}
                        </td>

                        <td> Hips  (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/><small>maximum extension of hips in standing position</small> </td>
                        <td>
                            {!! Form::text('hips', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'hips', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Hips Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.hips')])])!!}
                        </td>

                    </tr>
                    <tr role="row" class="heading">
                         <td> Left Thigh (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/><small>mid high circumference of right thigh</small></td>
                        <td>
                            {!! Form::text('thighs', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'thighs', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Left Thighs Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.left-thighs')])])!!}
                        </td>
                        <td> Right Thigh (in cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span><br/><small>mid high circumference of right thigh</small></td>
                        <td>
                            {!! Form::text('thighs_right', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'thighs_right', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Right Thighs Measurements.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.right-thighs')])])!!}
                        </td>
                        <td> Total CM loss<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('total_cm_loss', null, ['maxlength'=>8, 'class'=>'form-control', 'id'=>'total_cm_loss', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Total cm loss.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.total-cm-loss')])])!!}
                        </td>

                        <td> Therapist Name<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('therapist_name', null, ['maxlength'=>50, 'class'=>'form-control', 'id'=>'therapist_name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.therapist-name')])])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td colspan="8">
                            <button type="submit" class="btn green">{!! trans('admin::messages.submit') !!}</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>