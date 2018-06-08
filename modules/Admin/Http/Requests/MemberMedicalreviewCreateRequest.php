<?php

/**
 * The class for handling validation requests from CPRController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberMedicalreviewCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $regex = "/.*[a-z]+.*/i";
        return [
            'advice' => 'required|regex:' . $regex,
        ];
    }

    public function messages() {
        return [
            'advice.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.advice')]),
            'advice.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.advice')]),
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
