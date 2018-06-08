<div class="portlet light col-lg-12" id="measurement_record_table_view">
    <div class="portlet-title">

        {{--- Commenting this code as this can be managed using ACL ---}}
        {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions measurement-record-form-btn" >
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form-measurement"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.measurements-records')]) !!} </span></a>          </div>
        @endif
        {{--- @endif ---}}   

    </div>
    <div class="portlet-body">
        <div class=" box yellow">
            <div class="portlet-body">
                <div class="panel-group accordion" id="accordion3">


                    <?php $index1 = 1; ?>
                    @foreach($measurement_record_fields as $typeName => $subtypeArray)
                    @if($typeName != "Arm")
                    <?php
                    $class = "collapsed";
                    $collapsed_in = "collapse";

                    ?>
                    @else
                    <?php
                    $class = "";
                    $collapsed_in = "in";

                    ?>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled {!! $class !!}" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_{!! $index1 !!}" id="_{!! strtolower($typeName) !!}-table-anchor"> {!! $typeName !!} </a>
                            </h4>
                        </div>
                        <div id="collapse_3_{!! $index1 !!}" class="panel-collapse {!! $collapsed_in !!}">
                            <div class="panel-body">
                                <div class="portlet-body">
                                    <div class="table-container">

                                        <div id="_{!! strtolower($typeName) !!}-table"></div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php $index1++; ?>
                    @endforeach

                </div>
            </div>

        </div>
    </div>
</div>


<div class="portlet light col-lg-12" id="measurement_record_form_view" style="display: none;" >
    <div class="portlet-body">
        <div class=" box yellow">
            <div class="portlet-body">
                {!! Form::open(['route' => ['admin.cpr.store-measurement-record'], 'method' => 'post', 'data-toggle'=>'validator', 'class' => 'form-horizontal measurement-record-form',  'id' => 'create-measurement-record', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.measurement-record')]) ]) !!}
                <fieldset>
                    <div class="panel-group accordion" id="accordion4">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="_medical_review_form_table">
                                <thead>
                                </thead>
                                <tbody>
                                    <tr role="row" class="heading">
                                        <td> {!! trans('admin::controller/cpr.date') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                                        <td>
                                                {!! Form::select('date', [''=>'Please select Date'] +$sessionDateDataSpot, null,['autocomplete' => 'off', 'class'=>'select2me form-control', 'id' => 'date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.date')])]) !!}
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php $index_1 = 1; ?>
                        @foreach($measurement_record_fields as $typeName => $subtypeArray)
                        @if($typeName != "Arm")
                        <?php
                        $class2 = "collapsed";
                        $collapsed_in2 = "collapse";

                        ?>
                        @else
                        <?php
                        $class2 = "";
                        $collapsed_in2 = "in";

                        ?>
                        @endif
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled {!! $class2 !!}" data-toggle="collapse" data-parent="#accordion4" href="#collapse_4_{!! $index_1 !!}" > {!! $typeName !!} </a>
                                </h4>
                            </div>
                            <div id="collapse_4_{!! $index_1 !!}" class="panel-collapse {!! $collapsed_in2 !!}">
                                <div class="panel-body">
                                    <div class="portlet-body">
                                        <div class="table-container">
                                            <div class="table-actions-wrapper">
                                                <span></span>
                                                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                                <input id="data-search" type="search" class="form-control" placeholder="Search">
                                            </div>

                                            <table class="table table-striped table-bordered table-hover" id="{!! strtolower($typeName) !!}-table">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        @foreach($subtypeArray as $subtypeDetails)
                                                        <th style="vertical-align: top !important;">  {!! $subtypeDetails['title'] !!}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr role="row" class="heading">
                                                        <?php $index_2 = 1; ?>
                                                        @foreach($subtypeArray as $subtypeDetails)

                                                        <td>
                                                            <div class="input-group-recommendation parent-to-input" data-error-container="value[{!!$index_1!!}][{!!$index_2!!}]-error">
                                                                {!! Form::text('value['.$index_1.']['.$index_2.']', null, ['maxlength'=>5,'class'=>'form-control add-disabled min-one-required', 'id'=>'value['.$index_1.']['.$index_2.']', 'data-rule-number' => '10', 'data-msg-number'=>'Please enter valid '.$subtypeDetails['title'], 'requiredFromGroup'=>true])!!}
                                                                <div class="error" id="{!! 'value['.$index_1.']['.$index_2.']-error' !!}"></div>
                                                                </div>
                                                            </td>
                                                            <?php $index_2++; ?>
                                                            @endforeach
                                                        </tr>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php $index_1++; ?>
                            @endforeach
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <button type="submit" class="btn green" style="margin-left: 5px;margin-top: 10px;margin-bottom: 10px;">{!! trans('admin::messages.submit') !!}</button>
                                    <button type="button" class="btn default btn-collapse btn-collapse-form" style="margin-top: 10px;margin-bottom: 10px;">{!! trans('admin::messages.cancel') !!}</button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </div>

