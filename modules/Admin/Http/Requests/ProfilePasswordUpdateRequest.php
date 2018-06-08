<?php

namespace Modules\Admin\Http\Requests;

use Auth;

class ProfilePasswordUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        $cur_pwd = Auth::guard('admin')->user()->password;

        return [
            'current_password' => 'required|current_password:user,current_password,' . $cur_pwd,
            'password' => 'required|min:8|max:100|confirmed|current_new_password:user,current_password,' . $cur_pwd,
            'user_type_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.current_password')]),
            //'current_password.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/user.current_password'), 'number' => '8']),
            //'current_password.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.current_password'), 'number' => '100']),
            'current_password.current_password' => trans('admin::messages.current-password'),
            'password.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.password')]),
            'password.current_new_password' => trans('admin::messages.error-same', ['field-1' => trans('admin::controller/user.current-password'), 'field-2' => trans('admin::controller/user.new-password')]),
            'password.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/user.password'), 'number' => '8']),
            'password.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.password'), 'number' => '100']),
            'password.confirmed' => trans('admin::controller/user.password-confirmed'),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['current_password'] = filter_var($input['current_password'], FILTER_SANITIZE_STRING);
        $input['password'] = filter_var($input['password'], FILTER_SANITIZE_STRING);

        $this->replace($input);
    }
}
