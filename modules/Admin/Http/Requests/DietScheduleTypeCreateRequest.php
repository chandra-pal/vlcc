<?php

/**
 * The class for handling validation requests from ConfigCategoryController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class DietScheduleTypeCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'schedule_name' => 'required|max:50|alphaSpaces|unique:diet_schedule_types', //alphaSpaces
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'schedule_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-schedule-type.schedule_name')]),
            'schedule_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/diet-schedule-type.schedule_name'), 'number' => '50']),
            'schedule_name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/diet-schedule-type.schedule_name')]),
            'schedule_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/diet-schedule-type.schedule_name')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-schedule-typestatus')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-schedule-type.status')]),
            'start_time.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/diet-schedule-type.start-time')]),
            'end_time.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/diet-schedule-type.end-time')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['schedule_name'] = filter_var($input['schedule_name'], FILTER_SANITIZE_STRING);

        if (Auth::guard('admin')->check()) {
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
