<?php
/**
 * The class for admin user request for authentication for second form
 *
 * @author Manish Sahu<manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Requests\Auth;

use Modules\Admin\Http\Requests\Request;

class LoginRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required', 'password' => 'required',
        ];
    }
    
     public function messages()
    {
        return [
            'password.required' => 'Please enter Password.'
        ];
    }
}
