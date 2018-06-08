@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}
@stop

@section('content')

<div id="ajax-response-text"></div>

<!--@if(!empty(Auth::guard('admin')->user()->hasAdd))
@ include('admin::member-activity.create')
@endif-->
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form" style="text-align: center;margin-bottom: 10px;"></div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">Previous Booking History</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <!--div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/member-activity.member-activity')]) !!} </span></a>
        </div-->
        @endif
    </div>
	<div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <!-- <input id="data-search" type="search" class="form-control" placeholder="Search"> -->
            </div>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr role="row" class="heading">
					<th width='1%'>#</th>
                        <th width='15%'>Customer</th>
						<th width='15%'>Mobile Number</th>
                        <th width='15%'>Package</th>
                        <th width='15%'>Service</th>
						<th width='15%'>Appointment Date</th>
                        <th width='15%'>Start Time</th>
						<th width='15%'>End Time</th>
						<th width='15%'>Status</th>
						<th width='15%'>Created By</th>
						<th width='15%'>Updated By</th>
                    </tr>
                </thead>
                <tbody>
					@php($i=1)
					@foreach($var as $key=>$value)
					<tr>
					<td>{{$i}}</td>
					<td>{{$value->first_name}}</td>
					<td>{{$value->mobile_number}}</td>
					<td>@if($value->package_id == 0)
						<?php echo 'Others'; ?>
						@else
							{{$value->package_title}}
						@endif</td>
					<td>@if($value->package_id == 0)
							{{$value->service_name1}}
						@else
							{{$value->service_name}}
						@endif</td>
					<td>{{$value->session_date}}</td>
					<td>{{$value->start_time}}</td>
					<td>{{$value->end_time}}</td>
					<td>@if($value->status == 1)
						<?php echo 'Requested'; ?>
						@elseif($value->status == 2)
							<?php echo 'Booked';?>
							@elseif($value->status == 3)
								<?php echo 'Rejected';?>
							@elseif($value->status == 4)
							<?php echo 'Cancelled';?>
							@elseif($value->status == 5)
								<?php echo 'Completed';?>
							@elseif($value->status == 6)
							<?php echo 'Waiting List';?>
							@elseif($value->status == 7)
							<?php echo 'Confirmed';?>
							@else
							<?php echo 'No Response';?>
							
					@endif</td>
					<td>{{$value->Created_BY}}</td>
					<td>{{$value->Updated_BY}}</td>
					</tr>
					@php($i++)
					@endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
