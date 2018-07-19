
<div class="portlet box blue history" style="display:none">

	<div class="portlet-title togglelable">
        <div class="caption">
           Download Diet Plan History
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide date-Block" >

	 <span style="color: red; display: none;" id="date-range-error">Invalid Date Range</span>
	 <span style="color: red; display: none;" id="date-error">Please Select Date</span>
        {!! Form::open(['route' => ['admin.member-diet-plan.download-diet-history'], 'method' => 'get', 'target' => '_blank']) !!}
        <table class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody>
			<tr>
			<td><div class="input-group date from-date margin-bottom-5 " data-date="{{date('Y-m-d h:i:s')}}" style="width:70%">
				<input type="text" name ="from" class = "form-control form-filter input-sm"  id="from_date" placeholder = "From"  >
				<!--<span class="input-group-btn">
				<button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
				</span>-->
			</div></td>
			<td><div class="input-group date to-date " data-date="{{date('Y-m-d h:i:s')}}" style="width:70%">
				<input type="text" name="to" class = "form-control form-filter input-sm " id="to_date" placeholder = "To" >
                    <input type="hidden" id="selected_customer" name="customer" value="">
				<!--<span class="input-group-btn">
				<button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
				</span>-->
			</div></td>

                    <td colspan="5">
                        <button type="submit" class="btn green date-submit" ><i class="fa fa-download"></i><span class="hidden-480">Download Excel </span></button>
                        <button type="button" class="btn default date-hide">{!! trans('admin::messages.cancel') !!}</button>
                    </td>

                </tr>
            </tbody>
        </table>

        {!! Form::close() !!}
    </div>
</div>
