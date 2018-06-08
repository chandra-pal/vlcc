<?php

namespace Modules\Admin\Http\Requests;

use Auth;

class UserUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $id = $this->user->id;
        return [
            'username' => 'min:4|max:50|unique:admins,username,' . $id,
            'email' => 'max:100|email|unique:admins,email,' . $id,
            'first_name' => 'required|max:60',
            'last_name' => 'max:60',
            'password' => 'min:8|max:100|confirmed',
            'gender' => 'required',
            'avatar' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            'contact' => 'required|digits:10',
            'user_type_id' => 'required',
            'status' => 'required|numeric'
            //'center_id' => 'required'
        ];
    }

    public function messages() {
        return [
            'username.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.username')]),
            'username.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/user.username'), 'number' => '8']),
            'username.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.username'), 'number' => '50']),
            'username.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/user.username')]),
           // 'email.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.email')]),
            'email.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.email'), 'number' => '100']),
            'email.email' => trans('admin::messages.valid-enter', ['name' => trans('admin::controller/user.email')]),
            'email.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/user.email')]),
            'first_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]),
            'first_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.first-name'), 'number' => '60']),
            'last_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]),
            'last_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.last-name'), 'number' => '60']),
            'gender.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.gender')]),
            'contact.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.contact')]),
            'contact.digits' => 'The Mobile Number must contain 10 characters.',
            'contact.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/user.contact')]),
            'user_type_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/user.user_type')]),
            'password.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.password')]),
            'password.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/user.password'), 'number' => '8']),
            'password.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/user.password'), 'number' => '100']),
            'password.confirmed' => trans('admin::controller/user.password-confirmed'),
            'avatar.image' => trans('admin::messages.error-image', ['name' => trans('admin::controller/user.avatar')]),
            'avatar.mimes' => trans('admin::messages.mimes-name', ['name' => trans('admin::controller/user.avatar')]),
            'avatar.max' => trans('admin::messages.max-file-size-name', ['name' => trans('admin::controller/user.avatar')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.status')]),
            'status.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/usertype.status')]),
            //'center_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/user.center')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['password'] = filter_var($input['password'], FILTER_SANITIZE_STRING);
        $input['email'] = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $input['first_name'] = filter_var($input['first_name'], FILTER_SANITIZE_STRING);
        $input['last_name'] = filter_var($input['last_name'], FILTER_SANITIZE_STRING);
        $input['gender'] = filter_var($input['gender'], FILTER_SANITIZE_NUMBER_INT);
        $input['contact'] = filter_var($input['contact'], FILTER_SANITIZE_STRING);

        //Assign default value to checkbox if unchecked
        $input['skip_ip_check'] = (!empty($input['skip_ip_check'])) ? $input['skip_ip_check'] : 0;
        $input['skip_ip_check'] = filter_var($input['skip_ip_check'], FILTER_SANITIZE_NUMBER_INT);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);
        if (Auth::guard('admin')->check()) {
            $input['updated_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }
        $this->merge($input);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $action = $this->route()->getAction();

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->user->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
