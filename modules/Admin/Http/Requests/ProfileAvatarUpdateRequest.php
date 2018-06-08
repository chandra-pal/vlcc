<?php

namespace Modules\Admin\Http\Requests;

class ProfileAvatarUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes' => trans('admin::messages.mimes'),
            'avatar.max' => trans('admin::messages.max-file-size'),
        ];
    }
}
