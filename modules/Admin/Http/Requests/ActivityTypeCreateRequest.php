<?php

/**
 * The class for handling validation requests from ActivityTypeController::store()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class ActivityTypeCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $limit = config('settings.APP_CALORIES_LIMIT');
        return [
            'activity_type' => 'required|max:50|alphaSpaces|unique:activity_types', //alphaSpaces
            'calories' => 'required|min:1|integer|between:1,' . $limit,
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'activity_type.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/activity-type.activity-type')]),
            'activity_type.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/activity-type.activity-type'), 'number' => '50']),
            'activity_type.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/activity-type.activity-type')]),
            'activity_type.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/activity-type.activity-type')]),
            'calories.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/activity-type.calories')]),
            'calories.min' => "Calories should contain at least 1 number",
            'calories.max' => "Calories should not be greater than 50 numbers",
            'calories.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/activity-type.calories')]),
            'calories.between' => 'Please enter valid Calories.',
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/activity-type.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/activity-type.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['activity_type'] = filter_var($input['activity_type'], FILTER_SANITIZE_STRING);

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
