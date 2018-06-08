<?php

/**
 * The class for handling validation requests from DietPlanDetailController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class DietPlanDetailCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $serving = config('settings.APP_SERVING_SIZE_LIMIT');
        $this->sanitize();
        return [
            'diet_plan_id' => 'required|numeric', //alphaSpaces
            'diet_schedule_type_id' => 'required|numeric',
            'servings_recommended' => 'required|integer|between:1,' . $serving,
            'food_type_id' => 'required|numeric'
//            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'diet_plan_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-type')]),
            'diet_plan_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-type')]),
            'diet_schedule_type_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.diet-schedule-type')]),
            'diet_schedule_type_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan-detail.diet-schedule-type')]),
            'servings_recommended.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.serving-recommended')]),
            'servings_recommended.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/diet-plan-detail.serving-recommended')]),
            'servings_recommended.between' => 'Please enter valid Servings.',
            'food_type_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.food-type-list')]),
            'food_type_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan-detail.food-type-list')]),
//            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.status')]),
//            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/diet-plan-detail.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['diet_plan_id'] = filter_var($input['diet_plan_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_schedule_type_id'] = filter_var($input['diet_schedule_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['servings_recommended'] = filter_var($input['servings_recommended'], FILTER_SANITIZE_NUMBER_INT);
        $input['food_type_id'] = filter_var($input['food_type_id'], FILTER_SANITIZE_NUMBER_INT);

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
