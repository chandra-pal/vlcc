<?php
/**
 * The class for handling validation requests from ConfigCategoryController::update()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class ConfigCategoryUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return $rules = [
            'category' => 'required|max:50|unique:config_categories,category,' . $this->config_categories->id //alphaSpaces
        ];
    }

    public function messages()
    {
        return [
            'category.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/config-category.name')]),
            'category.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/config-category.name'), 'number' => '50']),
            'category.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/config-category.name')]),
            'category.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/config-category.name')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['category'] = filter_var($input['category'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->config_categories->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
