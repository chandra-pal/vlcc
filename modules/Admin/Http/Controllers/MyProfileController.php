<?php
/**
 * Class to manage profile specific actions
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Http\Requests\ProfileInfoUpdateRequest;
use Modules\Admin\Http\Requests\ProfileAvatarUpdateRequest;
use Modules\Admin\Http\Requests\ProfilePasswordUpdateRequest;

class MyProfileController extends Controller
{

    /**
     * The UserRepository instance.
     *
     * @var Modules\Admin\Repositories\UserRepository
     */
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * List all the data
     *
     * @return type
     */
    public function index()
    {
        $tabName = 'personal_info';

        return view('admin::myprofile.index', compact('tabName'));
    }

    /**
     * Update Profile Info
     * 
     * @param modules/Admin/Http/Requests/ProfileInfoUpdateRequest $request, modules/Admin/Models/User $user
     * 
     * @return json encoded response $response
     */
    public function update(ProfileInfoUpdateRequest $request, User $user)
    {
        $response = $this->repository->updateProfile($request->all(), $user);

        $response['formPlace'] = 'personal-info';
        $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/user.profile')]);
        $response['form'] = view('admin::myprofile.edit_info')->render();
        $response['userNameAvatar'] = view('admin::myprofile.username_avatar')->render();
        $response['userLoginInfo'] = view('admin::myprofile.userlogin_info')->render();

        return response()->json($response);
    }

    /**
     * Update avatar
     * 
     * @param modules/Admin/Http/Requests/ProfileAvatarUpdateRequest $request, modules/Admin/Models/User $user
     * 
     * @return json encoded response $response
     */
    public function updateAvatar(ProfileAvatarUpdateRequest $request, User $user)
    {
        //dd($request->all());
        $user = User::find(Auth::guard('admin')->user()->id);

        $response = $this->repository->updateAvatar($request->all(), $user);

        $response['formPlace'] = 'change-avatar';
        $response['message'] = trans('admin::messages.changed', ['name' => trans('admin::controller/user.picture')]);
        $response['form'] = view('admin::myprofile.change_picture', compact('response'))->render();
        $response['userNameAvatar'] = view('admin::myprofile.username_avatar')->render();
        $response['userLoginInfo'] = view('admin::myprofile.userlogin_info')->render();

        return response()->json($response);
    }

    /**
     * Change password
     * 
     * @param modules/Admin/Http/Requests/ProfilePasswordUpdateRequest $request, modules/Admin/Models/User $user
     * 
     * @return json encoded response $response
     */
    public function changePassword(ProfilePasswordUpdateRequest $request, User $user)
    {
        $user = User::find(Auth::guard('admin')->user()->id);

        $response = $this->repository->updateProfile($request->all(), $user);

        $response['formPlace'] = 'change-password';
        $response['message'] = trans('admin::messages.changed', ['name' => trans('admin::controller/user.password')]);
        $response['form'] = view('admin::myprofile.change_password', compact('response'))->render();

        return response()->json($response);
    }
}
