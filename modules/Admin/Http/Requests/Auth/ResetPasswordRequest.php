<?php namespace Modules\Admin\Http\Requests\Auth;

use Modules\Admin\Http\Requests\Request;

class ResetPasswordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
