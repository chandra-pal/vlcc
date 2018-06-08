<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8"/>
        <title> @yield('title', 'Administrator') | {!! config('settings.C_COMPANY_NAME'); !!}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="favicon.ico"/>
        {!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all') !!}
        {!! HTML::style( URL::asset('global/plugins/font-awesome/css/font-awesome.min.css') ) !!}
        {!! HTML::style( URL::asset('global/plugins/bootstrap/css/bootstrap.min.css') ) !!}
        {!! HTML::style( URL::asset('css/admin/login.css') ) !!}
        {!! HTML::style( URL::asset('global/css/components-md.css') ) !!}
        {{-- HTML::style( URL::asset('assets/global/css/plugins-md.css') ) --}}
        {{-- HTML::style( URL::asset('admintheme/layout/css/layout.css') ) --}}
        {{-- HTML::style( URL::asset('admintheme/layout/css/themes/default.css') ) --}}
        @yield('global-level-styles')
        @yield('page-level-styles')
        @yield('template-level-styles')
        @yield('styles')
        {!! HTML::style( URL::asset('css/admin/default-admin.css') ) !!}
    </head>
    <body class="page-md login">
        <div class="logo logo-bg">
            <img src="{!! URL::asset('images/logo.png') !!}" alt="iprogrammer solutions pvt ltd" style="height:35px;"/>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="copyright">
            {!! date('Y') !!} &copy; {!! config('settings.C_COMPANY_NAME'); !!}
        </div>
        <script>
            var adminUrl = '{!!URL::to("/admin")!!}';
        </script>
        {!! HTML::script( URL::asset('js/siteobj.js') ) !!}
        <!--[if lt IE 9]>
        {!! HTML::script( URL::asset('global/plugins/respond.min.js') ) !!}
        {!! HTML::script( URL::asset('global/plugins/excanvas.min.js') ) !!}
        <![endif]-->
        {!! HTML::script( URL::asset('global/plugins/jquery.min.js') ) !!}
        {!! HTML::script( URL::asset('global/plugins/jquery-migrate.min.js') ) !!}
        <!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        {!! HTML::script( URL::asset('global/plugins/jquery-ui/jquery-ui.min.js') ) !!}
        {!! HTML::script( URL::asset('global/plugins/bootstrap/js/bootstrap.min.js') ) !!}
        {!! HTML::script( URL::asset('global/plugins/uniform/jquery.uniform.min.js') ) !!}
        {!! HTML::script( URL::asset('global/scripts/metronic.js') ) !!}
        {!! HTML::script( URL::asset('js/admin/common.js') ) !!}
        @yield('global-level-scripts')
        @yield('page-level-scripts')
        @yield('template-level-scripts')
        @yield('scripts')
    </body>
</html>