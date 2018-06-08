<div id="ajax-response-text"></div>


@if($session_id == 0) 
<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <p>Session Not available for this member.</p>
    </div>
</div>    

@else

{{--- Commenting this code as this can be managed using ACL ---}}
{{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::cpr.create-session')
@endif
{{--- @endif ---}}

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/cpr.session-records')]) !!}</span>
        </div>

        {{--- Commenting this code as this can be managed using ACL ---}}        
        {{--- @if(!empty(Auth::guard('admin')->user()->hasAdd) && ($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8)) ---}}
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/cpr.session-records')]) !!} </span></a>
        </div>
        @endif
        {{--- @endif ---}}        
    </div>
    <div class="portlet-body">
        <input type="hidden" value="1" id="row_count" name="row_count">
        <input type="hidden" value="1" id="weight_row" name="weight_row">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
                <input id="session_date" type="hidden">
            </div>
            <table class="table table-striped table-bordered table-hover" id="session-record-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>Session No</th>
                        <th>BP</th>
                        <th>Date</th>
                        <th>Before Weight (kg)</th>
                        <th>After Weight (kg)</th>
                        <th>A Code</th>
                        <th style="width: 2%">Diet & Activity Deviation, if any</th>
                        <th>Therapist</th>
                        <th>OTP Verified</th>
                        <th>Service Execution</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

            {{--- Commenting this code as this can be managed using ACL ---}}
            {{--- @if($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8) ---}}
            @if(!empty(Auth::guard('admin')->user()->hasAdd))
<!--            <div class="form-actions">
                <div class="col-md-6" style="padding-left: 0px !important; margin-top: 20px;">
                    <div class="col-md-offset-6 col-md-9" style="margin-left: 0px !important;padding-left: 0px !important;">
                        <a class="btn green add-row-btn">+ Add Session</a>
                        <a class="btn green calculate-weight" href="javascript:;">Calculate Weight</a>
                    </div>
                </div>
            </div>-->
            @endif
            {{--- @endif ---}}

        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enter Comment</h4>
                </div>
                <div class="modal-body">
                    <!--<form id="add-ath-comment" name="add-ath-comment">-->
                    <textarea class="form-control" style="margin-bottom: 10px" name='ath_comment' id="ath_comment" rows="4" cols="30"  data-rule-required='true'></textarea>
                    <input type="hidden" id="session_programme_record_session_id" name="session_programme_record_session_id" value="0">
                    <span class="help-block help-block-error ath_comment_error" style="display:none;">Please Enter Comment.</span>
                    <button class="btn green submit_ath_comment" type="submit">Submit</button>
                    <!--</form>-->
                </div>
                <!--                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>-->
            </div>

        </div>
    </div>

    <div id="showVerifyOtpPopup" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 470px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Verify Otp</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="otp" id="otp" maxlength="4">
                    <input type="hidden" class="form-control" name="otp_id" id="otp_id">
                    <input type="hidden" class="form-control" name="session_programme_record" id="session_programme_record">
                    <span class="help-block help-block-error otp_error" style="display:none;">Please Enter Otp.</span>
                </div>
                <div class="modal-footer">
                    <button class="btn green submit_otp" type="submit">Submit</button>
                    <button type="button" data-dismiss="modal" class="btn default">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

@endif
