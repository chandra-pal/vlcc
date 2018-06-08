<?php
/**
 * The class for handling validation requests from MemberDietPlanController::store()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberDietPlanCreateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        foreach ($this->request->get('servings_recommended') as $key => $val) {
            $rules['servings_recommended.' . $key] = 'required';
        }
        return $rules;

//        return [
//            'id' => 'required|numeric',
//            'diet_plan_id' => 'required|numeric',
//            'servings_recommended.*' => 'required'
//        ];
    }

    public function messages()
    {
        $messages = [];
        foreach ($this->request->get('servings_recommended') as $key => $val) {
            $messages['servings_recommended.' . $key . '.required'] = 'Please Enter Servings Recommended. '.$key;
        }
        
        return $messages;

//return [
//            'id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-diet-plan.select-member')]),
//            'id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/member-diet-plan.select-member')]),
//            'diet_plan_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-diet-plan.select-plan-type')]),
//            'diet_plan_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/member-diet-plan.select-plan-type')]),
//            'servings_recommended.required' => 'Please Enter Servings Recommended'
//        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['id'] = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_plan_id'] = filter_var($input['diet_plan_id'], FILTER_SANITIZE_NUMBER_INT);

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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $status = Auth::guard('admin')->user()->can($action['as'], 'store');
        if (empty($status)) {
            abort(403);
        }
        return true;
    }
}
