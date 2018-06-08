<?php

/**
 * The class for handling validation requests from BeautyService::store()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class BeautyServiceCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'service_name' => 'required|max:100|unique:beauty_services',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'service_name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
           // 'service_name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
            'service_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/beauty-service.beauty-service'), 'number' => '100']),
            'service_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/beauty-service.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/beauty-service.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize() {
        $input = $this->all();

        $input['service_name'] = filter_var($input['service_name'], FILTER_SANITIZE_STRING);
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
