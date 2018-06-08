<?php

/**
 * The class for handling validation requests from MachineType::store()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MachineTypeCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'machine_type' => 'required|max:200|alphaSpaces|unique:machine_types', //alphaSpaces
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'machine_type.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/machine-type.machine-type')]),
            'machine_type.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/machine-type.machine-type')]),
            'machine_type.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/machine-type.machine-type'), 'number' => '200']),
            'machine_type.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/machine-type.machine-type')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/machine-type.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/machine-type.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize() {
        $input = $this->all();

        $input['machine_type'] = filter_var($input['machine_type'], FILTER_SANITIZE_STRING);
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
