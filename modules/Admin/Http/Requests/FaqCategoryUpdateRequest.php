<?php
/**
 * The class for handling validation requests from FaqCategoryController::Update()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class FaqCategoryUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        $id = $this->faq_categories->id;
        return [
            'name' => 'required|max:150|unique:faq_categories,name,' . $id, //alphaSpaces
            'position' => 'required|numeric|unique:faq_categories,position,' . $id,
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq-category.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/faq-category.name'), 'number' => '150']),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/faq-category.name')]),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/faq-category.name')]),
            'position.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq-category.position')]),
            'position.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/faq-category.position')]),
            'position.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/faq-category.position')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq-category.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/faq-category.status')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['position'] = filter_var($input['position'], FILTER_SANITIZE_NUMBER_INT);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->faq_categories->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
