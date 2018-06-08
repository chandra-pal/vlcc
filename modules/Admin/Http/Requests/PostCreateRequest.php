<?php
/**
 * The class for handling validation requests from PostController::store()
 * 
 * 
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class PostCreateRequest extends Request
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
            'name' => 'required|max:200|unique:posts', //alphaSpaces
            'description' => 'required',
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/post.name')]),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/post.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/post.name'), 'number' => '200']),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/post.name')]),
            'description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/post.description')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/post.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/post.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
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
