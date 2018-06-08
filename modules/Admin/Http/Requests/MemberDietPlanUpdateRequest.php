<?php

/**
 * The class for handling validation requests from MemberDietPlanController::Update()
 * 
 * 
 * @author Gauri Deshmukhe <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberDietPlanUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'diet_plan_id' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'diet_plan_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/member-diet-plan.diet-plan')]),
            'diet_plan_id.numeric' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/member-diet-plan.diet-plan')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        $input['diet_plan_id'] = filter_var($input['diet_plan_id'], FILTER_SANITIZE_NUMBER_INT);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->foods->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
