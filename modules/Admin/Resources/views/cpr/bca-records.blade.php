<div id="ajax-response-text" class="portlet-title"></div>

<b><span id='bca_alert_msg' style="color: red;"></span></b>

{{--- Commenting this code as this can be managed using ACL ---}}
{{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::cpr.create')
@endif
{{--- @endif ---}}

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/cpr.bca-data')]) !!}</span>
        </div>

        {{--- Commenting this code as this can be managed using ACL ---}}        
        {{--- @if(!empty(Auth::guard('admin')->user()->hasAdd) && ($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8)) ---}}
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.bca-data')]) !!} </span></a>
        </div>
        @endif
        {{--- @endif ---}}


    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" width="100%" id="bca-record-table">
                <thead>
                    <tr role="row" class="heading">
                        <th >#</th>
                        <th >BCA Image</th>
                        <th >Date</th>
                        <th >BMR (kcal/day)</th>
                        <th >Fat Wt (kg)</th>
                        <th >Fat (%)</th>
                        <th >Lean Wt (kg)</th>
                        <th >Lean (%)</th>
                        <th >Water (kg)</th>
                        <th >Water (%)</th>
                        <th >Target Weight (kg)</th>
                        <th >Target Fat (%)</th>
                        <th >BMI</th>
                        <th >Visceral Fat Level</th>
                        <th style="display: none;">Visceral Fat Area (cm)</th>
                        <th >Mineral</th>
                        <th >Protein</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
