<?php

/**
 * The class for handling validation requests from CPRController::store()
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class CPRCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'sometimes|max:50',
            'gender' => 'required',
            'dob' => 'required',
            'height' => 'required|numeric',
            'waist' => 'required|numeric',
            'weight' => 'required|numeric',
            'mobile' => 'required',
            'email' => 'email|max:50',
            'programme_needed' => 'required|numeric',
            'programme_booked' => 'required|numeric',
            'programme_booked_rs' => 'required|numeric',
            'payment_made' => 'required|numeric',
            'address' => 'max:255',
            'alternate_phone_number' => 'numeric|digits:10',
            'family_physician_number' => 'numeric|max:10',
        ];
    }

    public function messages() {
        return [
            'first_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.client-first-name')]),
            'first_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/cpr.client-first-name'), 'number' => '50']),
            'last_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.client-last-name')]),
            'last_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/cpr.client-last-name'), 'number' => '50']),
            'gender.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.gender')]),
            'dob.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.dob')]),
            'height.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.height')]),
            'height.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.height')]),
            'waist.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.waist')]),
            'waist.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.waist')]),
            'weight.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.weight')]),
            'weight.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.weight')]),
            'mobile.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.mobile')]),
            'programme_needed.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.programme-needed')]),
            'programme_needed.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.programme-needed')]),
            'programme_booked.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.programme-booked')]),
            'programme_booked.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.programme-booked')]),
            'programme_booked_rs.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.programme-booked-rs')]),
            'programme_booked_rs.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.programme-booked-rs')]),
            'payment_made.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.payment-made')]),
            'payment_made.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.payment-made')]),
            'email.email' => trans('admin::messages.valid-enter', ['name' => trans('admin::controller/cpr.email')]),
            'email.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/cpr.email'), 'number' => '50']),
            'address.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/cpr.address'), 'number' => '255']),
            'alternate_phone_number.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.alternate-phone-number')]),
            'alternate_phone_number.digits' => 'The Mobile Number must contain 10 characters.',
            'family_physician_number.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/cpr.family-physician-number')]),
            'family_physician_number.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/cpr.family-physician-number'), 'number' => '10']),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        if (Auth::guard('admin')->check()) {
            $input['dietician_id'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
            $input['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
            $input['height'] = filter_var($input['height'], FILTER_VALIDATE_FLOAT);
            $input['waist'] = filter_var($input['waist'], FILTER_VALIDATE_FLOAT);
            $input['weight'] = filter_var($input['weight'], FILTER_VALIDATE_FLOAT);
            $input['programme_needed'] = filter_var($input['programme_needed'], FILTER_VALIDATE_FLOAT);
            $input['programme_booked'] = filter_var($input['programme_booked'], FILTER_VALIDATE_FLOAT);
            $input['programme_booked_rs'] = filter_var($input['programme_booked_rs'], FILTER_VALIDATE_FLOAT);
            $input['payment_made'] = filter_var($input['payment_made'], FILTER_VALIDATE_FLOAT);
            $input['email'] = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
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
