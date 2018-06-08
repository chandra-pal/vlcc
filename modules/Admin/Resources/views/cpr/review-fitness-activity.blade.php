<div id="ajax-response-text"></div>

{{--- Commenting this code as this can be managed using ACL ---}}
{{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::cpr.create-review-fitness-activity')
@endif
{{--- @endif ---}}

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/cpr.review')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.review')]) !!} </span></a>
        </div>
        @endif
    </div>

    <table class="table table-striped table-bordered table-hover" id="">
        <thead> </thead>
        <tbody>
            <tr role="row" class="heading">
                <td colspan="3">Activity Code : </td>
            </tr>
            <tr role="row" class="heading">
                <td> A (Aerobic)</td>
                <td> SF (Strength + Flexibility)</td>
                <td> E (Endurance)</td>
            </tr>
            <tr role="row" class="heading">
                <td> S (Strength)</td>
                <td> PG (Posture & Gait)</td>
                <td> F (Flexibility)</td>
            </tr>
        </tbody>
    </table>


    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" width="100%" id="review-record-table">
                <thead>
                    <tr role="row" class="heading">
                        <th >#</th>
                        <th >{!! trans('admin::controller/cpr.review-date') !!}</th>
                        <th >{!! trans('admin::controller/cpr.static-posture-score') !!}</th>
                        <th >{!! trans('admin::controller/cpr.sit-and-reach-test') !!}</th>
                        <th >Right Shoulder Test</th>
                        <th >Left Shoulder Test</th>
                        <th >{!! trans('admin::controller/cpr.pulse') !!}</th>
                        <th >{!! trans('admin::controller/cpr.slr') !!}</th>
                        <th >{!! trans('admin::controller/cpr.activity-code') !!}</th>
                        <th >{!! trans('admin::controller/cpr.activity-duration') !!}</th>
                        <th >{!! trans('admin::controller/cpr.precautions-contraindications') !!}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
