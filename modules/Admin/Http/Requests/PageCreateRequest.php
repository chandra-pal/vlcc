<?php
/**
 *  
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request,
    HTML,
    Auth;

class PageCreateRequest extends Request
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
            'page_name' => 'required|min:2|unique:site_pages',
            'slug' => 'required|min:2|unique:site_pages',
            'page_url' => 'required|unique:site_pages',
            'browser_title' => 'required|min:2',
            'meta_keywords' => 'required|min:2',
            'meta_description' => 'required|min:5',
            'page_content' => 'required',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'page_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.page_name')]),
            'page_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/page.page_name')]),
            'page_name.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/page.page_name'), 'number' => '2']),
            'slug.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.slug')]),
            'slug.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/page.slug'), 'number' => '2']),
            'slug.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/page.slug')]),
            'page_url.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.page_url')]),
            'page_url.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/page.page_url')]),
            'browser_title.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.browser_title')]),
            'browser_title.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/page.browser_title'), 'number' => '2']),
            'meta_keywords.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.meta_keywords')]),
            'meta_keywords.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/page.meta_keywords'), 'number' => '2']),
            'meta_description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.meta_description')]),
            'meta_description.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/page.meta_description'), 'number' => '5']),
            'page_content.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.page_content')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/page.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['page_name'] = filter_var($input['page_name'], FILTER_SANITIZE_STRING);
        $input['slug'] = filter_var($input['slug'], FILTER_SANITIZE_STRING);
        $input['page_url'] = filter_var($input['page_url'], FILTER_SANITIZE_STRING);
        $input['browser_title'] = filter_var($input['browser_title'], FILTER_SANITIZE_STRING);
        $input['meta_keywords'] = filter_var($input['meta_keywords'], FILTER_SANITIZE_STRING);
        $input['meta_description'] = filter_var($input['meta_description'], FILTER_SANITIZE_STRING);
        $input['page_desc'] = HTML::entities($input['page_desc']);
        $input['page_content'] = HTML::entities($input['page_content']);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);

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
