<?php

/**
 * The repository class for managing member diet deviation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Deviation;
use Modules\Admin\Models\DietScheduleType;
use Modules\Admin\Models\Member;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;

class DeviationRepository extends BaseRepository {

    /**
     * Create a new DeviationRepository instance.
     *
     * @param  Modules\Admin\Models\Deviation $model
     * @return void
     */
    public function __construct(Deviation $deviation) {
        $this->model = $deviation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $request_details = DB::table('member_diet_deviations')
                ->join('members', 'member_diet_deviations.member_id', '=', 'members.id')
                ->join('diet_schedule_types', 'member_diet_deviations.diet_schedule_type_id', '=', 'diet_schedule_types.id')
                ->select('members.first_name as firstName', 'members.last_name as lastName', 'members.dietician_username as ditecianUsername', 'diet_schedule_types.id as ScheduleTypeID', 'diet_schedule_types.schedule_name as scheduleName', 'member_diet_deviations.id as devitionId', 'member_diet_deviations.member_id', 'member_diet_deviations.diet_schedule_type_id', 'member_diet_deviations.deviation_date', 'member_diet_deviations.calories_recommended', 'member_diet_deviations.calories_consumed')
                ->where('members.dietician_username', '=', $params['diteticianId'])
                ->where('member_diet_deviations.diet_schedule_type_id', '=', $params['schedule_type'])
                ->where('member_diet_deviations.deviation_date', '=', $params['date'])
                ->get();

        DB::setFetchMode(PDO::FETCH_CLASS);
        $response = collect($request_details);
        return $response;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function dataList($params = []) {

        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = [];
        $centerIds = DB::select("SELECT group_concat(center_id) as center_id FROM admin_centers WHERE user_id = " . $params['user_id'] . "");
        if (isset($centerIds[0]) && isset($centerIds[0]['center_id']) && $centerIds[0]['center_id'] != '') {
            $request_details = DB::table('member_diet_deviations')
                    ->join('members', 'member_diet_deviations.member_id', '=', 'members.id')
                    ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
                    ->join('diet_schedule_types', 'member_diet_deviations.diet_schedule_type_id', '=', 'diet_schedule_types.id')
                    ->select('members.first_name as firstName', 'members.last_name as lastName', 'members.dietician_username as ditecianUsername', 'diet_schedule_types.id as ScheduleTypeID', 'diet_schedule_types.schedule_name as scheduleName', 'member_diet_deviations.id as devitionId', 'member_diet_deviations.member_id', 'member_diet_deviations.diet_schedule_type_id', 'member_diet_deviations.deviation_date', 'member_diet_deviations.calories_recommended', 'member_diet_deviations.calories_consumed', 'vlcc_centers.center_name as centerName')
                    ->whereRaw("FIND_IN_SET(members.dietician_username,(select group_concat(a.username) FROM admin_centers as ac LEFT JOIN admins as a ON a.id=ac.user_id   WHERE center_id IN( " . $centerIds[0]['center_id'] . ")))")
                    ->where('member_diet_deviations.deviation_date', '=', $params['date'])
                    ->get();
        }

        DB::setFetchMode(PDO::FETCH_CLASS);
        $response = collect($request_details);
        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listFoodData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() {
            return Food::orderBY('id')->lists('food_name', 'id');
        });

        return $response;
    }

    public function getTotalDeviation($params) {
        $response = Deviation::select(['calories_recommended', 'calories_consumed'])->whereMemberId($params['mid'])->whereDeviationDate($params['date'])->orderBy('id')->get();
        return $response;
    }

    public function checkSchedulTypeId($params) {
        $response = Deviation::select('id')->whereDietScheduleTypeId($params['schedule_id'])->whereMemberId($params['mid'])->whereDeviationDate($params['date'])->get()->toArray();
        if ([] == $response) {
            $schedule_type_id = Deviation::select('id', 'diet_schedule_type_id')->whereMemberId($params['mid'])->whereDeviationDate($params['date'])->orderBy('created_at', 'desc')->first()->toArray();
            $deviationScheduleType = $schedule_type_id['diet_schedule_type_id'];
            $response = DietScheduleType::orderBY('id')->where('id', '>', $deviationScheduleType)->orderBy('id', 'asc')->lists('schedule_name', 'id');
            return $response;
        } else {
            return $response = '';
        }
    }

    /*     * *
     * get the count for dashboard
     */

    public function dataCount($params = []) {
        $response = '';
        if ($params['user_type_id'] == 4 || $params['user_type_id'] == 8) { //Dietician & Slimming Head 
            $memberIds = DB::select("SELECT group_concat(id) as id FROM members WHERE dietician_username = '" . $params['user_name'] . "'");
            if (isset($memberIds[0]) && isset($memberIds[0]->id) && $memberIds[0]->id != '') {
                $response = DB::table('member_diet_deviations')
                        ->join('members', 'member_diet_deviations.member_id', '=', 'members.id')
                        //->whereRaw('member_diet_deviations.member_id IN (' . $memberIds[0]->id . ')')
                        ->whereRaw('member_diet_deviations.member_id IN (' . rtrim($memberIds[0]->id,",") . ')')
                        ->where('member_diet_deviations.deviation_date', '=', $params['session_date'])
                        ->where('members.dietician_username', '=', $params['user_name'])
                        ->count();
            }
        } elseif ($params['user_type_id'] == 7 || $params['user_type_id'] == 9) { // ATH, Center Head
            $memberIds = DB::table('members')
                    ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
                    ->join('admin_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                    ->select(DB::raw('group_concat(members.id) as id'))
                    ->where('admin_centers.user_id', '=', $params['user_id'])
                    ->get();
            if (isset($memberIds[0]) && isset($memberIds[0]->id) && $memberIds[0]->id != '') {
                $response = DB::table('member_diet_deviations')
                        ->join('members', 'member_diet_deviations.member_id', '=', 'members.id')
                       // ->whereRaw('member_diet_deviations.member_id IN (' . $memberIds[0]->id . ')')
                       ->whereRaw('member_diet_deviations.member_id IN (' . rtrim($memberIds[0]->id, ",") .')')
                        ->where('member_diet_deviations.deviation_date', '=', $params['session_date'])
                        ->count();
            }
        } else {
            $response = collect();
        }
        return $response;
    }

}
