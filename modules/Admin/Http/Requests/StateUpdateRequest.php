<?php
/**
 * The class for handling validation requests from ConfigSettingController::update()
 * 
 * 
 * @author Nilesh Pangul <nileshp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class StateUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return [
            'name' => 'required|unique:states,name,' . $this->states->id . ',id,country_id,' . $this->states->country_id,
            'country_id' => 'required',
            'state_code' => 'required|unique:states,state_code,' . $this->states->id . ',id,country_id,' . $this->states->country_id,
        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/state.country')]),
            'name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/state.name')]),
            'name.unique' => trans('admin::messages.error-taken-state', ['name' => trans('admin::controller/state.name')]),
            'state_code.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/state.state_code')]),
            'state_code.unique' => trans('admin::messages.error-taken-state', ['name' => trans('admin::controller/state.state_code')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['country_id'] = filter_var($input['country_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['state_code'] = filter_var($input['state_code'], FILTER_SANITIZE_STRING);
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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->states->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
