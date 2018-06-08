<?php
/**
 * The class for LoginLogs repository used for log records for successful logged-in
 *
 * @author Manish S <manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\LoginLogs,
    Modules\Admin\Models\User,
    Illuminate\Support\Facades\Cache,
    Auth;

class LoginLogsRepository extends BaseRepository
{

    /**
     * The LoginLogs instance.
     *
     * @var Modules\Admin\Models\LoginLogs
     */
    protected $loginLogs;

    /**
     * Create a new model LoginLogs instance.
     *
     * @param Modules\Admin\Models\LoginLogs $loginLogs
     * @return void
     */
    public function __construct(LoginLogs $loginLogs)
    {
        $this->loginLogs = $loginLogs;
    }

    public function data($params = [])
    {
        $user = User::table();
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(LoginLogs::table(), User::table())->remember($cacheKey, $this->ttlCache, function() {
            return LoginLogs::with('User')->get();
        });
        
        return LoginLogs::leftJoin($user, 'login_logs.user_id', '=', $user . '.id')
                    ->select(['login_logs.*', $user . '.username'])
                    ->get();
        
        return $response;
    }

    /**
     * Insert login logs
     *
     * @param array $inputs
     */
    public function create($inputs)
    {
        return $this->loginLogs->create($inputs);
    }

    /**
     * Insert login logs
     *
     * @param array $inputs
     */
    public function update($id)
    {
        $loginLogsModel = LoginLogs::find($id);
        $loginLogsModel->out_time = date('Y-m-d h:i:s');
        $loginLogsModel->save();
    }

    /**
     * Group actions on LoginLogs
     *
     * @param  int  $status
     * @return int
     */
    public function groupAction($inputs)
    {
        if (empty($inputs['action'])) {
            return false;
        }
        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "delete":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $userId) {
                    $id = (int) $userId;
                    $user = LoginLogs::find($id);
                    if (!empty($user)) {
                        $user->delete();
                        $resultStatus = true;
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }
    
    public function getGroupActionData() {
        if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete))) {
            return ['' => 'Select', 1 =>'Delete'];
        } else {
            return ['' => 'Select'];        
        }
    }
}
