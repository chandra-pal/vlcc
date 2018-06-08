<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request,
    Illuminate\Support\Facades\Auth;

class LinksCreateRequest extends Request
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
            'link_name' => 'required|min:2|max:50|unique:links',
            'link_url' => 'required|min:2|max:50|link_route|unique:links',
            'page_header' => 'required|min:2|max:50|unique:links',
            'link_category_id' => 'required|integer',
            'position' => 'required|integer',
            'link_icon' => 'required',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'link_category_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/links.category')]),
            'link_category_id.integer' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/links.category')]),
            'link_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.link_name')]),
            'link_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/links.link_name')]),
            'link_name.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/links.link_name'), 'number' => '2']),
            'link_url.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.link_url')]),
            'link_url.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/links.link_url'), 'number' => '2']),
            'link_url.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/links.link_url'), 'number' => '50']),
            'link_url.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/links.link_url')]),
            'page_header.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.page_header')]),
            'page_header.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/links.page_header'), 'number' => '2']),
            'page_header.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/links.page_header'), 'number' => '50']),
            'page_header.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/links.page_header')]),
            'position.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.position')]),
            'position.integer' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/links.position')]),
            'link_icon.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.link_icon')]),
            'status.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/links.status')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['link_name'] = filter_var($input['link_name'], FILTER_SANITIZE_STRING);
        $input['link_url'] = filter_var($input['link_url'], FILTER_SANITIZE_STRING);
        $input['page_header'] = filter_var($input['page_header'], FILTER_SANITIZE_STRING);
        $input['page_text'] = filter_var($input['page_text'], FILTER_SANITIZE_STRING);
        $input['link_icon'] = filter_var($input['link_icon'], FILTER_SANITIZE_STRING);
        $input['position'] = filter_var($input['position'], FILTER_SANITIZE_NUMBER_INT);
        $input['link_category_id'] = filter_var($input['link_category_id'], FILTER_SANITIZE_NUMBER_INT);

        if (Auth::guard('admin')->check()) {
            $input['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }
        $this->merge($input);
    }
}
