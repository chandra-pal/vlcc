<?php
/**
 * The class for Ip login fail repository used for log records if user try to login from unauthenticate ip address
 *
 * @author Manish S <manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IpLoginFail;
use Cache;

class IpLoginFailRepository extends BaseRepository
{

    protected $ttlCache = 60; // minutes to leave Cache

    /**
     * The IpLoginFail instance.
     *
     * @var Modules\Admin\Models\IpLoginFail
     */
    protected $ipLoginFail;

    /**
     * Create a new IpLoginFail instance.
     *
     * @param  Modules\Admin\Models\IpLoginFail $ipLoginFail
     * @return void
     */
    public function __construct(IpLoginFail $ipLoginFail)
    {
        $this->ipLoginFail = $ipLoginFail;
    }

    /**
     * Insert unauthenticate ipaddress record
     * @param array $inputs
     */
    public function invalidIpAddressAttempt($inputs)
    {
        $insertData = [
            'ip_address' => $inputs['ip_address'],
            'username' => $inputs['username'],
            'access_time' => date('Y-m-d H:i:s')
        ];

        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $ifexits = Cache::tags(IpLoginFail::table())->remember($cacheKey, $this->ttlCache, function() use ($insertData) {
            return $this->ipLoginFail->where('ip_address', $insertData['ip_address'])->where('username', $insertData['username'])->first();
        });


        $this->ipLoginFail->create($insertData);
    }

    /**
     * Delete actions on faq categories
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs)
    {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $ipLoginFail = IpLoginFail::find($id);
            if (!empty($ipLoginFail)) {
                $ipLoginFail->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.notDeleted', ['name' => 'Login Log Details']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("Faq category could not be Deleted.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
