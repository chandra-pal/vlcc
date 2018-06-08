<div class="portlet light col-lg-12">
    <div class="">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover">
                <thead></thead>
                <tbody>
                    <tr role="row" class="heading">
                        <td> Select CSV File
                            <!--<span class="required" aria-required="true" style="color: #ff0000;">*</span>-->
                        </td>
                        <td>
                            {!! Form::file('bca_csv_file', ['id' => 'bca_csv_file', 'class' => 'form-control']) !!}
                            <span id="csv-file-error" style="color: #ff0000;"></span>
                        </td>

                    </tr>
                    <tr role="row" class="heading">
                        <td> Select BCA Image<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::file('bca_image', ['id' => 'bca_image', 'class' => 'form-control','data-rule-required'=>'true','data-msg-required'=>'Please Select BCA Image file']) !!}
                            <span id="bca-image-error" style="color: #ff0000;"></span>
                        </td>

                    </tr>
                </tbody>
            </table>



            <table class="table table-striped table-bordered table-hover" id="diet-plan-table">
                <thead>

                </thead>
                <tbody>
                    <tr role="row" class="heading">
                        <td> Date<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('recorded_date', null, ['class'=>'form-control skip review-date', 'readonly' => 'true', 'id'=>'recorded_date', 'data-rule-required'=>'true', 'data-msg-required'=>'Please Select BCA Date' ])!!}
                        </td>
                        <td> BMR (kcal/day)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('basal_metabolic_rate', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'basal_metabolic_rate', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Basal Metabolic Rate.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.basal-metabolic-rate')]) ])!!}
                        </td>
                        <td> Fat Wt (kg)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('fat_weight', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'fat_weight', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Fat Weight.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.fat-weight')]) ])!!}
                        </td>
                        <td> Fat (%)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('fat_percent', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'fat_percent', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Fat Percent.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.fat-percent')]) ])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> Lean Wt (kg)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('lean_body_mass_weight', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'lean_body_mass_weight', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Lean Body Mass Weight.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.lean-body-mass-weight')]) ])!!}
                        </td>

                        <td> Lean (%)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('lean_body_mass_percent', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'lean_body_mass_percent', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Lean Body Mass Percent.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.lean-body-mass-percent')]) ])!!}
                        </td>
                        <td> Water (kg)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('water_weight', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'water_weight', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Water Weight.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.water-weight')]) ])!!}
                        </td>

                        <td> Water (%)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('water_percent', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'water_percent', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Water Percent.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.water-percent')]) ])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading">
                        <td> Target Weight (kg)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('target_weight', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'target_weight', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Target Weight.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.target-weight')]) ])!!}
                        </td>
                        <td> Target Fat (%)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('target_fat_percent', null, ['class'=>'form-control', 'id'=>'target_fat_percent', 'data-rule-required'=>'true', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Target Fat Percent.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.target-fat-percent')])])!!}
                        </td>
                        <td> BMI<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                        <td>
                            {!! Form::text('body_mass_index', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'body_mass_index', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Body Mass Index.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.body-mass-index')]) ])!!}
                        </td>
                        <td> Visceral Fat Level
                            <!--<span class="required" aria-required="true" style="color: #ff0000;">*</span>-->
                        </td>
                        <td>
                            {!! Form::text('visceral_fat_level', null, ['maxlength'=>7,'class'=>'form-control', 'id'=>'visceral_fat_level', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Visceral Fat Level.', 'data-rule-required'=>'false', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.visceral-fat-level')]) ])!!}
                        </td>
                    </tr>
                    <tr role="row" class="heading" style="">
                        <td>Mineral</td>
                        <td>{!! Form::text('mineral', null, ['maxlength'=>5,'class'=>'form-control', 'id'=>'mineral', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Mineral.'])!!}</td>
                        <td>Protein</td>
                        <td>{!! Form::text('protein', null, ['maxlength'=>5,'class'=>'form-control', 'id'=>'protein', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid Protein.'])!!}</td>

                        <td> <label style="display: none">Visceral Fat Area (cm)<span class="required" aria-required="true" style="color: #ff0000;">*</span> </label></td>
                        <td>
                            {!! Form::hidden('visceral_fat_area', null, ['maxlength'=>5,'class'=>'form-control', 'id'=>'visceral_fat_area'])!!}


                        </td>

                        <td></td>
                        <td></td>
                    </tr>
                    <tr role="row" class="heading">
                        <td colspan="8">
                            <div class="alert alert-warning bca-alert" style="background-color: #f9e491;border-color: #f9e491; color: #c29d0b;">
                                <strong>Warning!</strong> <span id="bca_alert"> </span>
                            </div>
                            <button type="submit" class="btn green" style="margin-left: 5px; ">{!! trans('admin::messages.submit') !!}</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

