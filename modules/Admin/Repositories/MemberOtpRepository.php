<?php

/**
 * The repository class for managing member otp specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberOtp;
use Modules\Admin\Models\Member;
use Exception;
use Route;
use Log;
use Cache;

class MemberOtpRepository extends BaseRepository {

    /**
     * Create a new MemberOtpRepository instance.
     *
     * @param  Modules\Admin\Models\MemberOtp $model
     * @return void
     */
    public function __construct(MemberOtp $memberOtp) {
        $this->model = $memberOtp;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params['username']));
        //Cache::tags not suppport with files and Database
//        $response = Cache::tags(MemberOtp::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
        return MemberOtp::select([
                    'id', 'mobile_number', 'otp', 'sms_delivered', 'otp_used', 'attempt_count', 'created_at'
                ])->wheremobileNumber($params['contact'])->orderBy('id')->get();
//        });

        return $response;
    }

    public function getMemberContact($params = []) {
        return Member::whereId($params['member_id'])->lists('mobile_number');

        //$cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params['username']));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Member::table())->remember($cacheKey, $this->ttlCache, function()use($params) {
            return Member::whereId($params['member_id'])->lists('mobile_number');
        });
        //return $response;
    }

}
