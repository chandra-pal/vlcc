<?php

/**
 * The class for handling validation requests from StaffController::store()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class StaffCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'first_name' => 'required|alpha|max:60',
            'last_name' => 'required|alpha|max:60',
            'gender' => 'required',
            'mobile_number' => 'required|digits:10|numeric',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'first_name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/staff.first_name')]),
            'first_name.alpha' => trans('admin::messages.error-alpha', ['name' => trans('admin::controller/staff.first_name')]),
            'first_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/staff.first_name'), 'number' => '60']),
            'last_name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/staff.last_name')]),
            'last_name.alpha' => trans('admin::messages.error-alpha', ['name' => trans('admin::controller/staff.last_name')]),
            'last_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/staff.last_name'), 'number' => '60']),
            'gender.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff.gender')]),
            'mobile_number.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff.mobile_number')]),
            'mobile_number.digits' => 'The Mobile Number must contain 10 characters.',
            'mobile_number.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/staff.mobile_number')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/staff.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/staff.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize() {
        $input = $this->all();

        $input['first_name'] = filter_var($input['first_name'], FILTER_SANITIZE_STRING);
        $input['last_name'] = filter_var($input['last_name'], FILTER_SANITIZE_STRING);
        $input['gender'] = filter_var($input['gender'], FILTER_SANITIZE_NUMBER_INT);
        $input['mobile_number'] = filter_var($input['mobile_number'], FILTER_SANITIZE_STRING);
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
