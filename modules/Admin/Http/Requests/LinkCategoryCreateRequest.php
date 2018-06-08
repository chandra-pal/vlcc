<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request,
    Illuminate\Support\Facades\Auth;

class LinkCategoryCreateRequest extends Request
{

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return [
            'category' => 'required|min:2|max:50|unique:link_categories',
            'header_text' => 'min:5|max:255|required',
            'position' => 'required|integer|unique:link_categories',
            'category_icon' => 'required',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'category.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/link-category.category')]),
            'category.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/link-category.category'), 'number' => '2']),
            'category.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/link-category.category'), 'number' => '50']),
            'category.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/link-category.category')]),
            'header_text.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.header_text')]),
            'header_text.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/link-category.header_text'), 'number' => '5']),
            'header_text.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/link-category.header_text'), 'number' => '255']),
            'position.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.position')]),
            'position.integer' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/link-category.position')]),
            'position.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/link-category.position')]),
            'category_icon.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.category_icon')]),
            'status.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/link-category.status')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['category'] = filter_var($input['category'], FILTER_SANITIZE_STRING);
        $input['header_text'] = filter_var($input['header_text'], FILTER_SANITIZE_STRING);
        $input['category_icon'] = filter_var($input['category_icon'], FILTER_SANITIZE_STRING);
        $input['position'] = filter_var($input['position'], FILTER_SANITIZE_NUMBER_INT);

        if (Auth::guard('admin')->check()) {
            $input['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }
        $this->merge($input);
    }
}
