<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request,
    Illuminate\Support\Facades\Auth;

class UserTypeLinksCreateRequest extends Request
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
        return [
            'type_id' => 'required',
            'links' => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'type_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/usertypelinks.user_type')]),
            'links.required' => trans('admin::controller/usertypelinks.links-required')
        ];
    }
}
