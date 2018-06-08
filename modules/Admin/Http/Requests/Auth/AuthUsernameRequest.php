<?php
/**
 * The class for admin user request for authentication for first form
 *
 * @author Manish Sahu<manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests\Auth;

use Modules\Admin\Http\Requests\Request;
use Config;

class AuthUsernameRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $adminTable = Config::get('admin.auth.table');
        $logValue = $this->input('username');

        $logAccess = filter_var($logValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $logAccessRule = ($logAccess == 'email') ? 'required|email|exists:' . $adminTable . ',email' : 'required|exists:' . $adminTable;

        return [
            'username' => $logAccessRule,
            //'g-recaptcha-response' => 'required|recaptcha',
        ];
    }

    /**
     * Custom error messages
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => trans('admin::controller/login.username-req-field'),
            'username.exists' => trans('admin::controller/login.invalid-username'),
            'g-recaptcha-response.required' => trans('admin::controller/login.g-recaptcha-response-required'),
            'g-recaptcha-response.recaptcha' => trans('admin::controller/login.g-recaptcha-response-recaptcha')
        ];
    }
}
