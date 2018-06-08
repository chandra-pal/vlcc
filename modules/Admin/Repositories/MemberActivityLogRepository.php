<?php

/**
 * The repository class for managing member activity log specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberActivityLog;
use Modules\Admin\Models\ActivityType;
use Modules\Admin\Models\MemberActivityRecommendation;
use Exception;
use Route;
use Log;
use Cache;

class MemberActivityLogRepository extends BaseRepository {

    /**
     * Create a new MemberActivityLogRepository instance.
     *
     * @param  Modules\Admin\Models\MemberActivityLog $model
     * @return void
     */
    public function __construct(MemberActivityLog $memberActivityLog) {
        $this->model = $memberActivityLog;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
//        $response = Cache::tags(MemberActivityLog::table(), ActivityType::table())->remember($cacheKey, $this->ttlCache, function()use($params) {
        return MemberActivityLog::with('ActivityType')->whereMemberId($params['member_id'])->whereActivityDate($params['date'])->orderBy('id')->get();
//        });
//        return $response;
    }

    public function getMemberCalories($params) {
        $date = date('Y-m-d', strtotime($params['date']));
        return MemberActivityLog::select(['calories_burned'])->whereMemberId($params['client_id'])->whereActivityDate($date)->orderBy('id')->get();
    }

    public function getRecommendedCalories($params) {
        $date = date('Y-m-d', strtotime($params['date']));
        return MemberActivityRecommendation::select(['calories_recommended'])->whereMemberId($params['client_id'])->whereRecommendationDate($date)->orderBy('id')->get();
    }

}
