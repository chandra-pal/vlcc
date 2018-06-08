{{--*/ $userinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserInfo() /*--}}
<span class="username username-hide-on-mobile">{{ $userinfo->first_name }}</span>
@if(!empty($userinfo->avatar))
<img alt="" class="img-circle" src="{{ URL::asset('img/'.$userinfo->id.'/'.$userinfo->avatar) }}"/>
@else
<img alt="" class="img-circle" src="{{ URL::asset('images/default-user-icon-profile.png ') }}"/>
@endif