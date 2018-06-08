<?php
/**
 * The class for handling validation requests from FaqCategoryController::deleteAction()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class FaqCategoryDeleteRequest extends Request
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
            'ids' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/faq-category.category')]),
            'ids.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/faq-category.category')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['ids'] = filter_var($input['ids'], FILTER_SANITIZE_NUMBER_INT);

        $this->replace($input);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $action = $this->route()->getAction();

        $is_delete = Auth::guard('admin')->user()->can($action['as'], 'delete');
        $own_delete = Auth::guard('admin')->user()->can($action['as'], 'own_delete');

        if ($is_delete == 1 || (!empty($own_delete) && ($this->faq_categories->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
