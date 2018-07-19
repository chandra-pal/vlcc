<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>{!! trans('admin::messages.edit-name', ['name' => trans('admin::controller/session-bookings.session-bookings') ]) !!}
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($sessionBookings, ['route' => ['admin.session-bookings.update', $sessionBookings->id], 'method' => 'put', 'class' => 'form-horizontal panel session-bookings-form','id'=>'edit-session-bookings', 'msg' => trans('admin::messages.updated',['name'=>trans('admin::controller/session-bookings.session-bookings')]) ]) !!}


        <table class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody>
                @include('admin::session-bookings.form')
                <tr>
                    <td colspan="6">
                        <span id="resource-time-error" style="color: red;"></span>
                        @if($cancelFlag == 1)
                        <button type="submit" class="btn green submit_session" disabled>{!! trans('admin::messages.save') !!}</button>
                        @else
                        <button type="submit" class="btn green submit_session">{!! trans('admin::messages.save') !!}</button>
                        @endif

                        @if(isset($update_session) && $update_session==1)
                        <button type="button" class="btn default btn-collapse btn-collapse-form-edit redirect_to_session">{!! trans('admin::messages.cancel') !!}</button>
                        @else
                        <button type="button" class="btn default btn-collapse btn-collapse-form-edit">{!! trans('admin::messages.cancel') !!}</button>
                        @endif
                    </td>

                </tr>
            </tbody>
        </table>

        {!! Form::close() !!}
    </div>
</div>