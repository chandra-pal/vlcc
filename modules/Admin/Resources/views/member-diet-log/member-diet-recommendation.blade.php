<div class="add-form-main display-hide diet-recommendation-form">

    <div class="portlet-body form ">
        {!! Form::open(['route' => ['admin.member-diet-log.store'], 'method' => 'post', 'class' => 'form-horizontal config-category-form',  'id' => 'create-member-diet-log', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/member-diet-log.member-diet-log')]) ]) !!}
        {!! Form::hidden('row_count', $rowCount, array('class' => '', 'id'=>'row_count')) !!}
        {!! Form::hidden('diet_plan_id', $memberDietPlanId, array('class' => 'diet_plan_id', 'id'=>'diet_plan_id')) !!}
        <div class="caption">
            <i class="font-blue-sharp"></i>
            @if($style==0)
            <span class="caption-subject font-blue-sharp bold uppercase" id="deviation">Total Deviation : {{$deviation}}</span>
            @else
            <span class="caption-subject font-blue-sharp bold uppercase" id="deviation" style="display: none;">Total Deviation : {{$deviation}}</span>
            @endif
        </div>
        
        {{--- Commenting this code as this can be managed using ACL ---}}
        {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
            @if($style==0)
            <div class="schedule-type" >
                <label class="col-md-3 control-label">Select Schedule Type <span class="required" aria-required="true">*</span></label>
                <div class="col-md-4" id='schedule_dropdown'>
                    @include('admin::member-diet-log.dropdown')
                </div>
            </div>
            @else

            <div class="schedule-type" style="display: none;">
                <label class="col-md-3 control-label">Select Schedule Type <span class="required" aria-required="true">*</span></label>
                <div class="col-md-4" id='schedule_dropdown'>
                    @include('admin::member-diet-log.dropdown')
                </div>
            </div>
            @endif
        @endif

        <div class="portlet light col-lg-12" style="box-shadow: 0px 0px 0px 0px rgba(0,0,0,.03) !important;">

            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <!--input id="data-search" type="search" class="form-control" placeholder="Search"-->
                    </div>

                    <div class="caption" style="margin-bottom: 10px;padding-left: 20px;">
                        <i class="font-blue-sharp"></i>                        
                        <span class="caption-subject font-blue-sharp bold uppercase">Sent Recommendations</span>
                    </div>

                    <table class="table table-striped table-bordered table-hover" id="member-diet-log-recommendation-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>{!! trans('admin::controller/member-diet-log.food-type') !!}</th>
                                <th>{!! trans('admin::controller/member-diet-log.food-name') !!}</th>
                                <th>{!! trans('admin::controller/member-diet-log.recommended-servings') !!}</th>
                                <th>{!! trans('admin::controller/member-diet-log.measure') !!}</th>
                                <th>{!! trans('admin::controller/member-diet-log.calories') !!}</th>
                            </tr>
                        </thead>
                        <tbody id="member-diet-log-recommendation-table-body">

                        </tbody>

                        {{--- Commenting this code as this can be managed using ACL ---}}
                        {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
                        @if(!empty(Auth::guard('admin')->user()->hasAdd))
                            @if($style==0)
                                <tr class="child-row-1" >
                                    <td>
                                        {!! Form::select('food_type_id', [''=>'Select Food Type'] + $foodTypeLists, null,['class'=>'select2me form-control', 'id' => 'food_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food Type']) !!}
                                        <span id="food_type_error"></span>
                                    </td>
                                    <td id='member_food_type_list_data'>
                                        @include('admin::member-diet-log.member-food-type-list')
                                    </td>
                                    <td>
                                        {!! Form::text('servings_recommended[]', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control serving-recommended', 'id'=>'servings_recommended', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-diet-log.servings-recommended')]) ]) !!}
                                        <span id="serving_recommendation_error" class="help-block help-block-error"></span>

                                    </td>
                                    <td class="measure"> </td>
                                    <td class="calories"> </td>
                                </tr>
                                @else
                                <tr class="child-row-1" style="display: none;">
                                    <td>
                                        {!! Form::select('food_id[]', [''=>'Select Food'] + $foodList, null,['class'=>'form-control select-new-food', 'id' => 'food_id_1', 'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food']) !!}
                                        <span id="food_error"></span>
                                    </td>
                                    <td>
                                        {!! Form::text('servings_recommended[]', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control serving-recommended', 'id'=>'servings_recommended', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-diet-log.servings-recommended')]) ]) !!}
                                        <span id="serving_recommendation_error" class="help-block help-block-error"></span>

                                    </td>
                                    <td class="measure"> </td>
                                    <td class="calories"> </td>
                                </tr>
                            @endif
                        
                            <tr class="child-row-1">
                                <td colspan="5">
                                    @if($style==0)
                                    <button type="submit" class="btn green btn-panel">{!! trans('admin::messages.save') !!}</button>
                                    @else
                                    <button type="submit" class="btn green btn-panel" disabled>{!! trans('admin::messages.save') !!}</button>
                                    @endif
                                    <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                                </td>
                            </tr>
                        @endif

                    </table>
                    <div class="form-actionsgvb">
                        <div class="col-md-6" style="padding-left: 0px !important;">
                            <div class="col-md-offset-6 col-md-9" style="margin-left: 0px !important;padding-left: 0px !important;">
                                <!--<a class="btn green add-row-btn">+ Add Food</a>-->
                                <!--<button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>