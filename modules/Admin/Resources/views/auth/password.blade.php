@extends('admin::layouts.master')

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				@if(session()->has('status'))
      				@include('admin::partials/error', ['type' => 'success', 'message' => session('status')])
				@endif
				@if(session()->has('error'))
					@include('admin::partials/error', ['type' => 'danger', 'message' => session('error')])
				@endif	
				<hr>	
				<h2 class="intro-text text-center">{{ trans('front/password.title') }}</h2>
				<hr>
				<p>{{ trans('front/password.info') }}</p>		
				{!! Form::open(['url' => 'admin/password/email', 'method' => 'post', 'role' => 'form']) !!}	

					<div class="row">

						{!! Form::control('email', 6, 'email', $errors, trans('front/password.email')) !!}
						{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}
						{!! Form::text('address', '', ['class' => 'hpet']) !!}	
						
					</div>

				{!! Form::close() !!}

			</div>
		</div>
	</div>
@stop