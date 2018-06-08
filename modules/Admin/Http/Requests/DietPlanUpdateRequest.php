<?php

/**
 * The class for handling validation requests from DietPlanController::Update()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class DietPlanUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $limit = config('settings.APP_CALORIES_LIMIT');
        $this->sanitize();
        return [
            'plan_name' => 'required|max:20|regex:/^(?![0-9]*$)[a-zA-Z0-9\s\-()\/ ]+$/', //alphaSpaces
            'plan_type' => 'required|numeric',
            'calories' => 'required|integer|between:1,' . $limit,
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'plan_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan.diet-plan')]),
            'plan_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/diet-plan.diet-plan'), 'number' => '20']),
            'plan_name.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/diet-plan.diet-plan')]),
            'plan_type.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/diet-plan.plan-type')]),
            'plan_type.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan.plan-type')]),
            'calories.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/diet-plan.calories')]),
            'calories.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/diet-plan.calories')]),
            'calories.between' => 'Please enter valid Calories.',
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['plan_name'] = filter_var($input['plan_name'], FILTER_SANITIZE_STRING);
        $input['plan_type'] = filter_var($input['plan_type'], FILTER_SANITIZE_STRING);
        $input['calories'] = filter_var($input['calories'], FILTER_SANITIZE_STRING);

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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->faq_categories->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
