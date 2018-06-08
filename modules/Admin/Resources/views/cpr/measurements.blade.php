<div id="ajax-response-text"></div>

{{--- Commenting this code as this can be managed using ACL ---}}
{{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::cpr.create-measurement')
@endif
{{--- @endif ---}}  

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/cpr.measurements-records')]) !!}</span>
        </div>

        {{--- Commenting this code as this can be managed using ACL ---}}        
        {{--- @if(!empty(Auth::guard('admin')->user()->hasAdd) && ($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8)) ---}}
        @if(!empty(Auth::guard('admin')->user()->hasAdd))        
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.measurements')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="measurements-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th>Date</th>
                        <th>Neck</th>
                        <th>Chest</th>
                        <th>Left Arm</th>
                        <th>Right Arm</th>
                        <th>Tummy</th>
                        <th>Waist</th>
                        <th>Hips</th>
                        <th>Left Thigh</th>
                        <th>Right Thigh</th>
                        <th>Total cm loss</th>
                        <th>Therapist Name</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>