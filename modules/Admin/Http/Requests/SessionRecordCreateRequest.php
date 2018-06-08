<?php

/**
 * The class for handling validation requests from SessionController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class SessionRecordCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $regex = '/^\d{1,3}\/\d{1,3}$/';
        return [
            'recorded_date' => 'required',
            'bp' => 'sometimes|regex:' . $regex,
            'before_weight' => 'required',
            'after_weight' => 'required',
            'a_code' => 'required',
            'therapist_id' => 'required'
        ];
    }

    public function messages() {
        return [
            'recorded_date.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.recorded-date')]),
             'bp.regex' => 'Invalid Blood Pressure (BP) format',
            'before_weight.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.before_weight')]),
            'after_weight.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.after_weight')]),
            'a_code.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.a_code')]),
            'therapist_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.select-therapist')])
        ];
    }

    public function sanitize() {
        $input = $this->all();
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
