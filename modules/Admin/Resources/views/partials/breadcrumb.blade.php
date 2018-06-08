{{--*/
    $curRoute = (isset($curRoute))? $curRoute : "";
    $linkData = \Modules\Admin\Services\Helper\MenuHelper::getRouteByPage($curRoute);
    if(!empty($linkData['page_header'])){
        $menus = [
        ['label' => $linkData['category_name'], 'link' => 'javascript:;'],
        ['label' => $linkData['link_name'], 'link' => '']];
    }
/*--}}

@section('template-level-scripts')
@parent
<script>
    siteObjJs.admin.commonJs.constants.recordsPerPage = parseInt("{{--*/ echo $linkData['pagination'];/*--}}");
</script>
@stop

<div class="page-head">
    @if(Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "11" || Auth::guard('admin')->user()->userType->id == "8")
    {{--*/ $left_class='col-md-6';
        $right_class='col-md-6';  /*--}}
    @else
    {{--*/ $left_class='col-md-8';
        $right_class='col-md-4';  /*--}}
    @endif

    <div class="page-title {{$left_class}}">
        <h1>{!! $linkData['page_header'] !!}</h1>
        <input type="hidden" value="{{str_replace(' ', '_', $menus[0]['label'])}}" id="menu_name"/>
        <input type="hidden" value="{{str_replace(' ', '_', $linkData['link_name']).'_submenu'}}" id="submenu_name"/>
        <ul class="page-breadcrumb breadcrumb">
            <li>
                {!! link_to('/admin/dashboard', trans('admin::controller/user.admin')) !!}<i class="fa fa-circle"></i>
            </li>

            @foreach($menus as $menu)
            <li>
                @if(!empty($menu['link']) && $menu['link']=='javascript:;' )
                <a href="javascript:;">{{ $menu['label'] }}</a>
                @else
                <span class="text-muted"> {{ $menu['label'] }}</span>
                @endif
                <i class="fa fa-circle"></i>
            </li>
            @endforeach
        </ul>

        @if($linkData['page_text'])
        <!--        <h4>
                    {!! $linkData['page_text'] !!}
                </h4>-->
        @endif
    </div>

    <div class="float-right {{$right_class}}">
        @if(Auth::guard('admin')->user()->userType->id == "9" || Auth::guard('admin')->user()->userType->id == "5" || Auth::guard('admin')->user()->userType->id == "11" || Auth::guard('admin')->user()->userType->id == "8")
        @include('admin::partials.center-dropdown')
        @endif
        <div class="members_list">
            @include('admin::partials.customer-dropdown')
        </div>
    </div>

</div>