<?php

/**
 * The class for handling validation requests from CenterController::Update()
 *
 *
 * @author Gauri Deshmukhe <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class CenterUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
//        $id = $this->center->id;
        return [
            'center_name' => 'required|min:5|max:60',
            'country_id' => 'required', //alphaSpaces
            'state_id' => 'required',
            'city_id' => 'required',
            'address' => 'required|regex:/^(?![\W\s]+$).+$/m',
            'area' => 'required|max:255|regex:/^(?![\W\s]+$).+$/m',
            'pincode' => 'required|integer|between:1,10000000',
            'latitude' => 'required',
            'longitude' => 'required',
            'phone_number' => 'required|max:255|regex:/^(?![\W\s]+$).+$/m',
            'status' => 'required',
        ];
    }

    public function messages() {
        return [
            'center_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.center_name')]),
            'center_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/center.center_name'), 'number' => '60']),
            'center_name.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/center.center_name'), 'number' => '5']),
            'country_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.country')]),
            'state_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.state')]),
            'city_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.city')]),
            'address.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.address')]),
            'address.regex' => trans('admin::messages.error-regex-non-special-char', ['name' => trans('admin::controller/center.address')]),
            'area.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.area')]),
            'area.regex' => trans('admin::messages.error-regex-non-special-char', ['name' => trans('admin::controller/center.area')]),
            'pincode.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.pincode')]),
            'pincode.between' => 'Please enter valid Pin Code.',
            'latitude.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.latitude')]),
            'longitude.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.longitude')]),
            'phone_number.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.phone')]),
            'phone_number.regex' => trans('admin::messages.error-regex-non-special-char', ['name' => trans('admin::controller/center.phone')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/center.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        $input['center_name'] = filter_var($input['center_name'], FILTER_SANITIZE_STRING);
        $input['country_id'] = filter_var($input['country_id'], FILTER_SANITIZE_STRING);
        $input['state_id'] = filter_var($input['state_id'], FILTER_SANITIZE_STRING);
        $input['city_id'] = filter_var($input['city_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['address'] = filter_var($input['address'], FILTER_SANITIZE_STRING);
        $input['area'] = filter_var($input['area'], FILTER_SANITIZE_STRING);
        $input['pincode'] = filter_var($input['pincode'], FILTER_SANITIZE_STRING);
        $input['latitude'] = filter_var($input['latitude'], FILTER_SANITIZE_STRING);
        $input['longitude'] = filter_var($input['longitude'], FILTER_SANITIZE_STRING);
        $input['phone_number'] = filter_var($input['phone_number'], FILTER_SANITIZE_STRING);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->center->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
