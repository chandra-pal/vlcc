{{--*/ $usertypeinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserWithType() /*--}}
{{--*/ $usercenterinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserCenter($usertypeinfo->id) /*--}}
<div class="portlet light profile-sidebar-portlet">

    <div class="profile-userpic">
        @if(!empty($userinfo->avatar))
        <img src="{{ URL::asset('img/'.$userinfo->id.'/'.$userinfo->avatar) }}" class="img-responsive" alt="" />
        @else
        <img src="{{ URL::asset('images/default-user-icon-profile.png ') }}" class="img-responsive" alt="" />
        @endif
    </div>
    <div class="profile-usertitle">
        <div class="profile-usertitle-name"><span class="tooltips" data-placement="right" data-original-title="First Name (Username)">{{ $usertypeinfo->first_name }} ({{ $usertypeinfo->username }})</span></div>        
        <div class="label label-info tooltips" data-placement="right" data-original-title="Designation">{{ $usertypeinfo->userType->name }} </div>
        @foreach($usercenterinfo as $key => $center)
        <div class="profile-usertitle-name">
            {!! $center['center_name'] !!}
        </div>
        @endforeach
    </div>
    <br />
</div>