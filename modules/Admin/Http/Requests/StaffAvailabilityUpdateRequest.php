<?php

/**
 * The class for handling validation requests from StaffAvailabilityController::Update()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class StaffAvailabilityUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'center_id' => 'required',
            'staff_id' => 'required',
            'availability_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            //'break_time' => 'required',
            'carry_forwarded' => 'numeric',
            'carry_forwarded_days' => 'numeric'
        ];
    }

    public function messages() {
        return [
            'center_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/staff-availability.center')]),
            'staff_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/staff-availability.staff')]),
            'availability_date.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.availability_date')]),
            'start_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.start_time')]),
            'end_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.end_time')]),
           // 'break_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.break_time')]),
            'carry_forwarded.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/staff-availability.carry_forwarded')]),
            'carry_forwarded_days.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/staff-availability.carry_forwarded_days')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        if (Auth::guard('admin')->check()) {
            $input['updated_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
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

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->staff_availability->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
