<?php

/**
 * The repository class for managing center specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Center;
use Modules\Admin\Models\Country;
use Modules\Admin\Models\State;
use Modules\Admin\Models\City;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;

class CenterRepository extends BaseRepository {

    /**
     * Create a new CenterRepository instance.
     *
     * @param  Modules\Admin\Models\Center $model
     * @return void
     */
    public function __construct(Center $center) {
        $this->model = $center;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
//        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() {
//            return Center::select([
//                        'id', 'address', 'area', 'city_id', 'state_id', 'country_id', 'phone_number', 'status'
//                    ])->orderBy('id')->get();
//        });

        $response = Cache::tags(Center::table(), City::table(), Country::table(), State::table())->remember($cacheKey, $this->ttlCache, function() {
            return Center::with('States', 'Country', 'City')->orderBy('country_id')->orderBy('state_id')->orderBy('id')->get();
        });


        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listAllCategoriesData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() {
            // return Center::orderBY('id')->lists('id', 'address', 'area', 'city_id', 'state_id', 'country_id', 'pincode', 'latitude', 'longitude', 'phone_number', 'status');
            return Center::orderBY('center_name')->lists('area', 'id');
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, $user_id = null) {
        try {
            $center = new $this->model;

            $allColumns = $center->getTableColumns($center->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $center->$key = $value;
                }
            }
            $save = $center->save();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/center.center')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/center.center')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/center.center')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/center.center')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an center.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Center $center
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $center) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($center->$key)) {
                    $center->$key = $value;
                }
            }
            $save = $center->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/center.center')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/center.center')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/center.center')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/center.center')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on center
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $center = Center::find($id);
            if (!empty($center)) {
                $center->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/center.center')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/center.center')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to get Members List of selected center
    public function getMembersList($center_id) {
        $membersList = [];
        DB::setFetchMode(PDO::FETCH_KEY_PAIR);

        $membersList = DB::select("SELECT members.id as member_id,
        CONCAT_WS(' ',members.mobile_number, '-', members.first_name, members.last_name) AS full_name
        FROM vlcc_centers centers
        LEFT OUTER JOIN members members ON
        centers.crm_center_id = members.crm_center_id
        WHERE centers.id=" . $center_id . " AND members.status=1");

        DB::setFetchMode(PDO::FETCH_CLASS);
        return $membersList;
    }

    /**
     * get the center id of particuler user id
     * @param type $member_id
     * @return type
     */
    public function getCenterId($member_id){
        $centerId = '';
        $centerId = DB::select("SELECT centers.id as center_id
            FROM vlcc_centers as centers
        LEFT OUTER JOIN members as members ON
        centers.crm_center_id = members.crm_center_id
        WHERE members.id=".$member_id);
        return $centerId;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listCenterData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() {
            return Center::orderBY('center_name')->lists('center_name', 'id');
            // return Center::orderBY('area')->lists('area', 'id');
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listLoggedInUsersCenters($logged_in_user_id) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $centers = DB::table('admins')
                ->join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                ->join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                ->select('vlcc_centers.center_name', 'vlcc_centers.id')
                ->where('admins.id', '=', $logged_in_user_id)
                ->get();

        $arrCenters = collect($centers)->toArray();
        $arrCenters1 = array_column($arrCenters, 'center_name', 'id');
        return $arrCenters1;
    }
}
