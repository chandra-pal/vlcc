<div class="portlet box add-form-main fitness-assessment" style="box-shadow: 0 2px 2px 0px rgba(0, 0, 0, 0);">
    <div class="caption">
        <h2>{!! trans('admin::controller/cpr.skin-hair-analysis-form') !!}</h2>
        <hr>
    </div>

    <div class="portlet-body form">

        {!! Form::open(['route' => ['admin.cpr.store-skin-hair-analysis'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal skin-hair-analysis-form',  'id' => 'create-skin-hair-analysis', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.cpr')]) ]) !!}
        <fieldset>
            <table class="table table-striped table-bordered table-hover" id="skin-hair-analysis-table">
                <thead>
                </thead>
                <tbody>
                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.skin-type') !!}</td>
                        <td colspan="5">
                            <div class="col-md-3 checkbox-container-skin-type"><label class="radio-inline">{!! Form::checkbox('skin_type[]', 1, null, ['class' => 'skin_type']) !!} Normal</label></div>
                            <div class="col-md-3 checkbox-container-skin-type"><label class="radio-inline">{!! Form::checkbox('skin_type[]', 2, null, ['class' => 'skin_type']) !!} Dry</label></div>
                            <div class="col-md-3 checkbox-container-skin-type"><label class="radio-inline">{!! Form::checkbox('skin_type[]', 3, null, ['class' => 'skin_type']) !!} Oily</label></div>
                            <div class="col-md-3 checkbox-container-skin-type"><label class="radio-inline">{!! Form::checkbox('skin_type[]', 4, null, ['class' => 'skin_type']) !!} Combination</label></div>
                        </td>


                    </tr>
                    <tr role="row" class="heading">
                        <td colspan="6"> <b>Skin Condition : </b></td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.oily-skin') !!}</td>
                        <td colspan="5">

                            <div class="col-md-3 checkbox-container-skin-condition"><label class="radio-inline">{!! Form::checkbox('skin_condition[]', 1, null, ['class' => 'skin_condition']) !!} Blackhead</label></div>
                            <div class="col-md-3 checkbox-container-skin-condition"><label class="radio-inline">{!! Form::checkbox('skin_condition[]', 2, null, ['class' => 'skin_condition']) !!} Whitehead</label></div>
                            <div class="col-md-3 checkbox-container-skin-condition"><label class="radio-inline">{!! Form::checkbox('skin_condition[]', 3, null, ['class' => 'skin_condition']) !!} Papule</label></div>
                            <div class="col-md-3 checkbox-container-skin-condition"><label class="radio-inline">{!! Form::checkbox('skin_condition[]', 4, null, ['class' => 'skin_condition']) !!} Pustule</label></div>
                            <div class="col-md-3 checkbox-container-skin-condition"><label class="radio-inline">{!! Form::checkbox('skin_condition[]', 5, null, ['class' => 'skin_condition']) !!} Nodule Cyst</label></div>

                        </td>


                    </tr>

                    <tr role="row" class="heading">
                        <td colspan="6"> <b>{!! trans('admin::controller/cpr.hyperpigmentation') !!} : </b></td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.hyperpigmentation-type') !!}</td>
                        <td>
                            {!! Form::text('hyperpigmentation_type', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'hyperpigmentation_type', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.hyperpigmentation-type')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.hyperpigmentation-type')]) ])!!}
                        </td>

                        <td> {!! trans('admin::controller/cpr.hyperpigmentation-size') !!}</td>
                        <td>
                            {!! Form::text('hyperpigmentation_size', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'hyperpigmentation_size', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.hyperpigmentation-size')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.hyperpigmentation-size')]) ])!!}
                        </td>


                        <td> {!! trans('admin::controller/cpr.hyperpigmentation-depth') !!}</td>
                        <td>
                            {!! Form::text('hyperpigmentation_depth', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'hyperpigmentation_depth',  'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.hyperpigmentation-depth')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.hyperpigmentation-depth')]) ])!!}
                        </td>

                    </tr>

                    <tr role="row" class="heading">
                        <td colspan="6"> <b>{!! trans('admin::controller/cpr.scars') !!} : </b></td>
                    </tr>


                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.scars-depth') !!}</td>
                        <td>
                            {!! Form::text('scars_depth', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'scars_depth', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.scars-depth')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.scars-depth')]) ])!!}
                        </td>

                        <td> {!! trans('admin::controller/cpr.scars-size') !!}</td>
                        <td>
                            {!! Form::text('scars_size', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'scars_size', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.scars-size')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.scars-size')]) ])!!}
                        </td>


                        <td> {!! trans('admin::controller/cpr.scars-pigmented') !!}</td>
                        <td>
                            <div class="radio-list">
                                <label class="radio-inline radio-container">{!! Form::radio('scars_pigmented', 1, true, ['class' => 'skin_condition']) !!} Yes</label>
                                <label class="radio-inline radio-container">{!! Form::radio('scars_pigmented', 2, null, ['class' => 'skin_condition']) !!} No</label>
                            </div>


                        </td>

                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.fine-lines-wrinkles') !!}</td>
                        <td  colspan="2">
                            {!! Form::text('fine_lines_and_wrinkles', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'fine_lines_and_wrinkles', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.fine-lines-wrinkles')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.fine-lines-wrinkles')]) ])!!}
                        </td>

                        <td> {!! trans('admin::controller/cpr.skin-curvature') !!}</td>
                        <td  colspan="2">
                            {!! Form::text('skin_curvature', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'skin_curvature', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.skin-curvature')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.skin-curvature')]) ])!!}
                        </td>

                    </tr>

                    <tr role="row" class="heading">

                        <td> Other Marks</td>
                        <td colspan="5">
                            <div class="col-md-3 checkbox-container-other-marks"><label class="checkbox-inline">{!! Form::checkbox('other_marks[]', 1, null, ['class' => 'other_marks']) !!} Moles</label></div>
                            <div class="col-md-3 checkbox-container-other-marks"><label class="checkbox-inline">{!! Form::checkbox('other_marks[]', 2, null, ['class' => 'other_marks']) !!} Warts</label></div>
                            <div class="col-md-3 checkbox-container-other-marks"><label class="checkbox-inline">{!! Form::checkbox('other_marks[]', 3, null, ['class' => 'other_marks']) !!} Naevus</label></div>
                        </td>
                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.hair-type') !!}</td>
                        <td colspan="2">
                            <div class="col-md-6 checkbox-container-hair-type"><label class="radio-inline">{!! Form::checkbox('hair_type[]', 1, null, ['class' => 'hair_type']) !!} Normal</label></div>
                            <div class="col-md-6 checkbox-container-hair-type"><label class="radio-inline">{!! Form::checkbox('hair_type[]', 2, null, ['class' => 'hair_type']) !!} Dry</label></div>
                            <div class="col-md-6 checkbox-container-hair-type"><label class="radio-inline">{!! Form::checkbox('hair_type[]', 3, null, ['class' => 'hair_type']) !!} Brittle</label></div>
                            <div class="col-md-6 checkbox-container-hair-type"><label class="radio-inline">{!! Form::checkbox('hair_type[]', 4, null, ['class' => 'hair_type']) !!} Oily</label></div>
                        </td>


                        <td> {!! trans('admin::controller/cpr.scalp-condition') !!}</td>
                        <td colspan="2">

                            <div class="col-md-6 checkbox-container-scalp-condition"><label class="radio-inline">{!! Form::checkbox('condition_of_scalp[]', 1, null, ['class' => 'condition_of_scalp']) !!} Oily</label></div>
                            <div class="col-md-6 checkbox-container-scalp-condition"><label class="radio-inline">{!! Form::checkbox('condition_of_scalp[]', 2, null, ['class' => 'condition_of_scalp']) !!} Dry</label></div>
                            <div class="col-md-6 checkbox-container-scalp-condition"><label class="radio-inline">{!! Form::checkbox('condition_of_scalp[]', 3, null, ['class' => 'condition_of_scalp']) !!} Dandruff</label></div>
                            <div class="col-md-6 checkbox-container-scalp-condition"><label class="radio-inline">{!! Form::checkbox('condition_of_scalp[]', 4, null, ['class' => 'condition_of_scalp']) !!} Infection</label></div>
                        </td>

                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.hair-density') !!}</td>
                        <td colspan="5">
                            {!! Form::text('hair_density', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'hair_density', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.hair-density')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.hair-density')]) ])!!}

                        </td>

                    </tr>


                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.hair-shaft') !!}</td>
                        <td colspan="5">
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 1, null, ['class' => 'condition_of_hair_shaft']) !!} Dandruff</label></div>
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 2, null, ['class' => 'condition_of_hair_shaft']) !!} Chemically Treated</label></div>
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 3, null, ['class' => 'condition_of_hair_shaft']) !!} Thermally Damaged</label></div>
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 4, null, ['class' => 'condition_of_hair_shaft']) !!} Split Ends</label></div>
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 5, null, ['class' => 'condition_of_hair_shaft']) !!} Colour</label></div>
                            <div class="col-md-4 checkbox-container-hair-shaft"><label class="radio-inline">{!! Form::checkbox('condition_of_hair_shaft[]', 6, null, ['class' => 'condition_of_hair_shaft']) !!} Dry / Dehydrated</label></div>
                        </td>

                    </tr>


                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.allergy-history') !!}</td>
                        <td colspan="2">
                            {!! Form::textarea('history_of_allergy', null, ['rows'=>'2','minlength'=>2,'maxlength'=>255,'class'=>'form-control min-one-required', 'id'=>'history_of_allergy', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.allergy-history')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.allergy-history')]) ])!!}
                        </td>

                        <td> {!! trans('admin::controller/cpr.conclusion') !!}</td>
                        <td colspan="2">
                            {!! Form::textarea('conclusion', null, ['rows'=>'2','minlength'=>2,'maxlength'=>255,'class'=>'form-control min-one-required', 'id'=>'conclusion', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.conclusion')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.conclusion')]) ])!!}
                        </td>


                    </tr>

                    <tr role="row" class="heading">
                        <td> {!! trans('admin::controller/cpr.name') !!}</td>
                        <td colspan="3">
                            {!! Form::text('skin_and_hair_specialist_name', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control min-one-required', 'id'=>'skin_and_hair_specialist_name', 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.name')]) ])!!}
                        </td>
                        <td> {!! trans('admin::controller/cpr.analysis-date') !!}</td>
                        <td>
                            {!! Form::text('analysis_date', null, ['readonly' => 'true','minlength'=>2,'maxlength'=>100,'class'=>'form-control review-date min-one-required', 'id'=>'analysis_date', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.analysis-date')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.analysis-date')]) ])!!}
                        </td>

                    </tr>

                    {{--- Commenting this code as this can be managed using ACL ---}}
                    {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
                    @if(!empty(Auth::guard('admin')->user()->hasAdd))
                    <tr role="row" class="heading">
                        <td colspan="6">
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

