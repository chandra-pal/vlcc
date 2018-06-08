<?php

/**
 * The class for handling validation requests from RoomAvailabilityController::store()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class RoomAvailabilityCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'center_id' => 'required',
            'room_id' => 'required',
            'availability_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            // 'break_time' => 'required',
            'carry_forwarded' => 'numeric',
            'carry_forwarded_days' => 'numeric'
        ];
    }

    public function messages() {
        return [
            'center_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/room-availability.center')]),
            'room_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/room-availability.room')]),
            'availability_date.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/room-availability.availability_date')]),
            'start_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/room-availability.start-time')]),
            'end_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/room-availability.end-time')]),
            // 'break_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/room-availability.break-time')]),
            'carry_forwarded.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/room-availability.carry-forwarded')]),
            'carry_forwarded_days.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/room-availability.carry-forwarded_days')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        if (Auth::guard('admin')->check()) {
            $input['center_id'] = filter_var($input['center_id'], FILTER_SANITIZE_NUMBER_INT);
            $input['room_id'] = filter_var($input['room_id'], FILTER_SANITIZE_NUMBER_INT);
            $input['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }

        $this->merge($input);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $action = $this->route()->getAction();

        $status = Auth::guard('admin')->user()->can($action['as'], 'store');
        if (empty($status)) {
            abort(403);
        }
        return true;
    }

}
