<?php

namespace Modules\Admin\Http\Requests;

class ProfileInfoUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        $id = $this->myprofile->id;

        return [
            'email' => 'max:100|email|unique:admins,email,' . $id,
            'first_name' => 'required|alpha|max:60',
            'last_name' => 'required|alpha|max:60',
            'password' => 'min:8|max:100|confirmed',
            'gender' => 'required',
            'contact' => 'required|numeric|digits:10',
            'user_type_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'email.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.email'), 'number' => '100']),
            'email.email' => trans('admin::messages.valid-enter', ['name' => trans('admin::controller/user.email')]),
            'email.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/user.email')]),
            'first_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]),
            'first_name.alpha' => trans('admin::messages.error-alpha', ['name' => trans('admin::controller/user.first-name')]),
            'first_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.first-name'), 'number' => '60']),
            'last_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]),
            'last_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.last-name'), 'number' => '60']),
            'gender.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.gender')]),
            'contact.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.contact')]),
            'contact.digits' => 'The Mobile Number must contain 10 characters.',
            'contact.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/user.contact')]),
            'user_type_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/user.user_type')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['email'] = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $input['first_name'] = filter_var($input['first_name'], FILTER_SANITIZE_STRING);
        $input['last_name'] = filter_var($input['last_name'], FILTER_SANITIZE_STRING);
        $input['gender'] = filter_var($input['gender'], FILTER_SANITIZE_NUMBER_INT);
        $input['contact'] = filter_var($input['contact'], FILTER_SANITIZE_STRING);
        $input['user_type_id'] = filter_var($input['user_type_id'], FILTER_SANITIZE_NUMBER_INT);

        $this->replace($input);
    }
}
