<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.medical-review')]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.cpr.store-medical-review'], 'method' => 'post', 'class' => 'form-horizontal config-category-form',  'id' => 'create-medical-review', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/cpr.medical-review')]) ]) !!}
        <fieldset>
            <div class="portlet light col-lg-12">
                <div class="">
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <span></span>
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <input id="data-search" type="search" class="form-control" placeholder="Search">
                        </div>

                        <table class="table table-striped table-bordered table-hover" id="_medical_review_form_table">
                            <thead>

                            </thead>
                            <tbody>
                                <tr role="row" class="heading">
                                    <td> {!! trans('admin::controller/cpr.session-date') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                                    <td>
                                        {!! Form::text('session_date', \Carbon\Carbon::now()->format('d-m-Y'), ['readonly' => 'true', 'class'=>'form-control skip-date', 'id'=>'session_date'])!!}

                                    </td>
                                    <td> {!! trans('admin::controller/cpr.advice') !!}<span class="required" aria-required="true" style="color: #ff0000;">*</span> </td>
                                    <td>
                                        {!! Form::textarea('advice', null, ['rows'=> '3','minlength'=>2,'maxlength'=>256,'class'=>'form-control', 'id'=>'advice', 'data-rule-maxlength'=>'256', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.food-allergy')]),'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.advice')]), 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.advice')]) ])!!}
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