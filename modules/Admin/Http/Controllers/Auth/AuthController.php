<?php

/**
 * The class for admin user authentication
 *
 *
 * @author Manish Sahu<manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Modules\Admin\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\Auth\LoginRequest;
use Modules\Admin\Http\Requests\Auth\AuthUsernameRequest;
use Modules\Admin\Services\MaxValueDelay;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\IpAddressRepository;
use Modules\Admin\Repositories\AuthRepository;
use Modules\Admin\Repositories\LoginLogsRepository;
use Auth;
use Validator;
use Event;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {

    use AuthenticatesAndRegistersUsers;

    /**
     * variable to redirect after logout
     * @var string
     */
    protected $redirectAfterLogout = 'admin/auth/login';

    /**
     * The IpAddressRepository instance.
     *
     * @var Modules\Admin\Repositories\IpAddressRepository
     */
    protected $ipAddressRepository;

    /**
     * The AuthRepository instance.
     *
     * @var Modules\Admin\Repositories\AuthRepository
     */
    protected $authRepository;

    /**
     * The LoginLogsRepository instance.
     *
     * @var Modules\Admin\Repositories\LoginLogsRepository
     */
    protected $loginLogsRepository;
    protected $redirectTo = '/admin';
    protected $guard = 'admin';

    /**
     * The UserRepository instance.
     *
     * @var Modules\Admin\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Modules\Admin\Repositories\IpAddressRepository $ipAddressRepository
     * @param  Modules\Admin\Repositories\AuthRepository $authRepository
     * @param  Modules\Admin\Repositories\UserRepository $userRepository
     * @param  Modules\Admin\Repositories\LoginLogsRepository $loginLogsRepository
     *
     * @return void
     */
    public function __construct(IpAddressRepository $ipAddressRepository, AuthRepository $authRepository, UserRepository $userRepository, LoginLogsRepository $loginLogsRepository) {
        $this->ipAddressRepository = $ipAddressRepository;
        $this->authRepository = $authRepository;
        $this->loginLogsRepository = $loginLogsRepository;
        $this->userRepository = $userRepository;
        $this->middleware('guestAdmin', ['except' => 'getLogout']);
    }

    /**
     * Show login form
     *
     * @return Response
     */
    public function getLogin() {
        return view('admin::auth.username-validate');
    }

    /**
     * Validate admin username and valid access ip address
     *
     * @param AuthUsernameRequest $request
     * @param MaxValueDelay $maxValueDelay
     * @return response
     */
    public function authUsername(AuthUsernameRequest $request, MaxValueDelay $maxValueDelay) {
        $response = [];
        $result = false;
        $username = $request->input('username');
        if ($maxValueDelay->check($username)) {
            $response['errormsg'] = ['response' => trans('admin::controller/login.maxattempt')];
        } else {

            // Validate for skip ip address
            if ($this->userRepository->isIpaddressCheckRequire($username)) {

                $fields = ['ip_address' => $request->getClientIp()];
                $rules = ['ip_address' => 'required|exists:ip_addresses,ip_address,status,1'];
                $messages = [
                    'ip_address.required' => trans('admin::controller/login.invalid-ipaddress'),
                    'ip_address.exists' => trans('admin::controller/login.invalid-ipaddress')
                ];
                $validator = Validator::make($fields, $rules, $messages);
                if ($validator->fails()) {
                    # The given data did not pass validation
                    $response['errormsg'] = $validator->errors()->getMessages();
                    $request->merge([
                        'ip_address' => $fields['ip_address']
                    ]);
                    $res = Event::fire("ipaddressfail.attempt", array($request->all()));
                    $response['message'] = $res;
                } else {
                    $result = true;
                    $response['loginform'] = view('admin::auth.login')->render();
                }
            } else {
                $result = true;
                $response['loginform'] = view('admin::auth.login')->render();
            }
        }

        #increment and cache the max attempts
        $maxValueDelay->increment($username);

        $response['success'] = $result;

        return response()->json($response);
    }

    /**
     * Handle a login request to the application.
     *
     * @param App\Http\Requests\LoginRequest  $request
     * @param MaxValueDelay $maxValueDelay
     * @return Response
     */
    public function postLogin(LoginRequest $request, MaxValueDelay $maxValueDelay) {
        $response = [];
        $result = false;
        $username = $request->input('username');
        $redirectToUrl = '/admin/dashboard';

        if ($maxValueDelay->check($username)) {
            $response['errormsg'] = ['response' => trans('admin::controller/login.maxattempt')];
        } else {
            $test = Auth::guard('admin')->attempt(['email' => 'webmaster', 'password' => '7u8i9o0p']);

            $logAccess = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $credentials = [$logAccess => $username, 'password' => $request->input('password'), 'status' => 1];

            //var_dump(Auth::guard('admin')->attempt($credentials));
            //var_dump(Auth::guard('admin')->user());
            //var_dump(Auth::guard('admin')->check());
            //exit;

            if (Auth::guard('admin')->attempt($credentials)) {

                #insert record into login logs table
                $this->authRepository->insertLoginLogs($request);

                $result = true;

                $intended_url = Session::get('url.intended', url('/admin/dashboard'));
                Session::forget('url.intended');

                $redirectToUrl = $intended_url;
            } else {
                $result = false;
                $response['errormsg'] = ['login-error-msg' => trans('admin::controller/login.invalidaccess')];
                $maxValueDelay->increment($username);
            }
        }
        $response['success'] = $result;
        $response['redirectToUrl'] = $redirectToUrl;

        return response()->json($response);
    }

    /**
     * Log the user out of the application.
     *
     */
    public function getLogout() {
        if (Auth::guard('admin')->user()) {
            $this->authRepository->updateLoginLogs();
        }
        Auth::guard('admin')->logout();
        Session::flush();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
