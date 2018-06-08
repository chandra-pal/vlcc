
<div class="portlet box blue history" style="display:none">
   
	<div class="portlet-title togglelable">
        <div class="caption">
           Previous booking history 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide date-Block" >
	 <span style="color: red; display: none;" id="date-range-error">Invalid Date Range</span>
	 <span style="color: red; display: none;" id="date-error">Please Select Date</span>
        {!! Form::open(['route' => ['admin.session-bookings.booking-history'], 'method' => 'get', 'target' => '_blank']) !!}       
        <table class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody>
			<tr>
			<td><div class="input-group date from-date margin-bottom-5 " data-date="{{date('Y-m-d h:i:s')}}" style="width:70%">
				<input type="text" name ="from" class = "form-control form-filter input-sm"  id="time-from" placeholder = "From"  >
				<!--<span class="input-group-btn">
				<button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
				</span>-->
			</div></td>
			<td><div class="input-group date to-date " data-date="{{date('Y-m-d h:i:s')}}" style="width:70%">
				<input type="text" name="to" class = "form-control form-filter input-sm " id="time-to" placeholder = "To" >
				<!--<span class="input-group-btn">
				<button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
				</span>-->
			</div></td>
                    <td colspan="5"> 
                        <button type="submit" class="btn green date-submit" >{!! trans('admin::messages.submit') !!}</button>
                        <button type="button" class="btn default date-hide">{!! trans('admin::messages.cancel') !!}</button>
                    </td>

                </tr>
            </tbody>
        </table>

        {!! Form::close() !!}
    </div>
</div>
