@if(isset($update_session))
{{--*/ $style = 'display:none'; /*--}}
@else
{{--*/ $style = 'display:block'; /*--}}
@endif

<div class="portlet box blue add-form-main" style="{{$style}}">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>Add Appointment
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide" >
        {!! Form::open(['route' => ['admin.session-bookings.store'], 'method' => 'post', 'class' => 'form-horizontal session-bookings-form',  'id' => 'create-session-bookings', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/session-bookings.session-bookings')]) ]) !!}
        <table class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody>
                @include('admin::session-bookings.form')

                <tr>
                    <td colspan="5">
                        <span id="resource-time-error" style="color: red;"></span>
                        <button type="submit" class="btn green submit_session">{!! trans('admin::messages.submit') !!}</button>
                        <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                    </td>

                </tr>
            </tbody>
        </table>

        {!! Form::close() !!}
    </div>
</div>