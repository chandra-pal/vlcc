<?php

namespace Modules\Admin\Http\Requests;

use Auth;

class UserTypeLinksUpdateRequest extends Request
{

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
