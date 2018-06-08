{{--*/ $sidebarLinks = \Modules\Admin\Services\Helper\MenuHelper::getSideBarLinks() /*--}}
<style>
    .page-sidebar-closed li.heading {
        display: none;
    }

    li.heading {
        border-top: solid 1px #5C9ACF;
    }
</style>
<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <li class="text-center menuExpandCollapseBtn">
        <div class="btn-group btn-group-solid ">
            <button type="button" class="btn btn-xs blue menu-expand-all"><i class="fa fa-folder-open"></i>Expand All</button>
            <button type="button" class="btn btn-xs blue menu-collapse-all">Collapse All<i class="fa fa-folder"></i></button>
        </div>
    <li class="start ">
        @if(Auth::guard('admin')->user()->user_type_id == 4 || Auth::guard('admin')->user()->user_type_id == 8)
        <a href="{!! URL::to('admin/members') !!}" id="my-clients">
            <i class="icon-users"></i><span class="title">My Clients</span>
        </a>
        @else
        <a href="{!! URL::to('admin/dashboard') !!}" id="Dashboard">
            <i class="icon-home"></i><span class="title">Dashboard</span>
        </a>
        @endif

    </li>
    @foreach ($sidebarLinks as $key=>$sidebarLinkMenu)
    @if(sizeof($sidebarLinkMenu['link_categories'])>0)
    <li class="heading">
<!--        <i class="icon-screen-desktop" style="color: #5C9ACF;"></i>-->
        <h3 class="uppercase">{{ $sidebarLinkMenu['menu_group_name'] }}</h3>
    </li>
    @endif

    @foreach ($sidebarLinkMenu['link_categories'] as $key=>$sidebarLink)
    <li id="{{str_replace(' ', '_', $sidebarLink[0]['category'])}}">
        <a href="javascript:;">
            <i class="{{ $sidebarLink[0]['category_icon'] }}"></i>
            <span class="title">{{ $sidebarLink[0]['category'] }}</span>
            <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
            @foreach ($sidebarLink as $sidebarLinkItem)
            <li>
                {{--*/
                if(Route::has($sidebarLinkItem['link_url'])) {
                    $link_href = route($sidebarLinkItem['link_url']);
                } else {
                    $link_href = '';
                }
                /*--}}
                <a href="{{ $link_href }}" id="{{str_replace(' ', '_', $sidebarLinkItem['link_name']).'_submenu'}}"><i class="{{ $sidebarLinkItem['link_icon'] }}"></i>
                    <div class="menu-link">
                        {{ $sidebarLinkItem['link_name'] }}
                    </div>
                </a>

            </li>
            @endforeach
        </ul>
    </li>
    @endforeach
    @endforeach
</ul>