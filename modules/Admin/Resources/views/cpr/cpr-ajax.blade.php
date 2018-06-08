{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div class="table-container">
    <div class="">
        <span></span>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input id="data-search" type="search" class="form-control" placeholder="Search">
        <input type="hidden" name="session_id"  id ='session_id' value="{!! $session_id !!}">
        <input type="hidden" name="member_id"  id ='member_id' value="{!! $member_id !!}">
        <input type="hidden" name="package_id"  id ='package_id' value="{!! $package_id !!}">
        <input type="hidden" name="check_bca_data_flag"  id ='check_bca_data_flag' value="99">
        <input type="hidden" name="logged_in_user_id"  id ='logged_in_user_id' value="{!! $logged_in_user_id !!}">
        <input type="hidden" name="logged_in_user_type"  id ='logged_in_user_type' value="{!! $logged_in_by_user_type !!}">
    </div>

    <div id="smartwizard">
        <ul>
            <li><a href="#step-1">Personal Info</a></li>
            <li><a href="#step-2">BCA Records</a></li>
            <li><a href="#step-3">Measurements</a></li>
            <li><a href="#step-4">Slimming Programme Records</a></li>
            <li><a href="#step-5">Dietary Assessment</a></li>
            <li><a href="#step-6">Fitness Assessment</a></li>
            <li><a href="#step-7">Medical Assessment</a></li>
            <li><a href="#step-8">Skin & Hair Analysis</a></li>
            <li><a href="#step-9">Review of Fitness Assessment & Activity Pattern</a></li>

        </ul>
        <div>
            <div id="step-1">
                @include('admin::cpr.personal-info')
            </div>
            <div id="step-2">
                @include('admin::cpr.bca-records')
            </div>
            <div id="step-3">
                @include('admin::cpr.measurements')
            </div>
            <div id="step-4">
                @include('admin::cpr.session-records')
            </div>
            <div id="step-5">
                @include('admin::cpr.dietary-assessment')
            </div>
            <div id="step-6">
                @include('admin::cpr.fitness-assessment')
            </div>
            <div id="step-7">
                @include('admin::cpr.medical-assessment')
            </div>
            <div id="step-8">
                @include('admin::cpr.skin-hair-analysis')
            </div>
            <div id="step-9">
                @include('admin::cpr.review-fitness-activity')
            </div>
        </div>
    </div>

</div>
