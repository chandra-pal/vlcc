<?php

/**
 * The class for handling validation requests from MachineController::update()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MachineUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $centerIds = $this->center_id;
        $centerIds = implode(",", $centerIds);
        return [
            'machine_type_id' => 'required',
            'name' => 'required|max:200|regex:/^(?![0-9]*$)[a-zA-Z0-9\s\-()\/ ]+$/|unique_machine_name_center_wise:' . $centerIds . ',machine_id,' . $this->machines->id . ',name,' . $this->name,
            //'name' => 'required|max:200|unique_machine_name_center_wise:'.$centerIds,
            'center_id' => 'required',
            'description' => 'regex:/^(?![0-9]*$)[a-zA-Z0-9\s\-()\/ ]+$/',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'machine_type_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/machine.machine-type')]),
            'name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/machine.name')]),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/machine.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/machine.name'), 'number' => '200']),
            'name.unique_machine_name_center_wise' => 'Machine name already exists for Selected Center.',
            'name.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/machine.name')]),
            'center_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/machine.center')]),
            'description.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/machine.machine-description')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/machine.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/machine.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize() {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        //  $input['center_id'] = filter_var($input['center_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->machines->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
