<?php
/**
 * The class for Auth repository used for authentication login.
 *
 * @author Manish S <manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\User,
    Illuminate\Support\Facades\Auth,
    Modules\Admin\Models\LoginLogs,
    Illuminate\Support\Facades\Cache,
    Modules\Admin\Repositories\LoginLogsRepository;

class AuthRepository extends BaseRepository
{

    protected $ttlCache = 60; // minutes to leave Cache

    /**
     * The User Model instance.
     *
     * @var Modules\Admin\Models\User
     */
    protected $user;

    /**
     * Create a new AuthRepository instance.
     *
     * @param  Modules\Admin\Models\User $user
     * @return void
     */
    public function __construct(User $user, LoginLogsRepository $loginLogRepository)
    {
        $this->user = $user;
        $this->loginLogRepository = $loginLogRepository;
    }

    public function insertLoginLogs($request)
    {
        $insertLoginLogs = [
            'user_id' => Auth::guard('admin')->user()->id,
            'ip_address' => $request->getClientIp(),
            'last_access_time' => date('Y-m-d H:i:s'),
            'in_time' => date('Y-m-d H:i:s')
        ];
        $this->loginLogRepository->create($insertLoginLogs);
    }

    public function updateLoginLogs()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . (json_encode(Auth::guard('admin')->user()->id));
        //Cache::tags not suppport with files and Database
        $data = Cache::tags(LoginLogs::table())->remember($cacheKey, $this->ttlCache, function() {
            $loginLogsModel = LoginLogs::whereUserId(Auth::guard('admin')->user()->id)->orderBy('id', 'desc')->first();
            if (!empty($loginLogsModel)) {
                $loginLogs = $loginLogsModel->toArray();
                $this->loginLogRepository->update($loginLogs['id']);
            }
        });
    }
}
