<?php
/**
 * The class for handling validation requests from ConfigSettingController::store()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class ConfigSettingCreateRequest extends Request
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
            'config_category_id' => 'required|numeric|min:1',
            'description' => 'required|max:255',
            'config_constant' => 'required|alphaDash|max:100|unique:config_settings',
            'config_value' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'config_category_id.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/config-setting.config-cat')]),
            'config_category_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/config-setting.config-cat')]),
            'config_category_id.min' => trans('admin::messages.error-minvalue-number-id', ['name' => trans('admin::controller/config-setting.config-cat'), 'number' => '1']),
            'description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.label-desc')]),
            'description.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/config-setting.label-desc'), 'number' => '255']),
            'config_constant.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.const-name')]),
            'config_constant.alpha_dash' => trans('admin::messages.error-alpha-dash', ['name' => trans('admin::controller/config-setting.const-name')]),
            'config_constant.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/config-setting.const-name'), 'number' => '255']),
            'config_constant.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/config-setting.const-name')]),
            'config_value.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.const-value')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['config_category_id'] = filter_var($input['config_category_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
        $input['config_constant'] = filter_var($input['config_constant'], FILTER_SANITIZE_STRING);
        $input['config_value'] = filter_var($input['config_value'], FILTER_SANITIZE_STRING);
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
