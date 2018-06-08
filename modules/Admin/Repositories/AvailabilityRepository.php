<?php

/**
 * The repository class for managing activity type specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Availability;
use Exception;
use Route;
use Log;
use Cache;

class AvailabilityRepository extends BaseRepository {

    /**
     * Create a new AvailabilityRepository instance.
     *
     * @param  Modules\Admin\Models\Availability $model
     * @return void
     */
    public function __construct(Availability $availability) {
        $this->model = $availability;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        //Cache:flush();
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Availability::table())->remember($cacheKey, $this->ttlCache, function() {
            return Availability::select()->orderBy('id')->get();
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
            $availableDates = explode(',', $inputs['availability_date']);
            foreach ($availableDates as $date) {
                $availability = new $this->model;
                $whereClause = [
                    'dietician_id' => $inputs['dietician_id'],
                    'availability_date' => date('Y-m-d', strtotime($date))
                ];
                $availabilityInsertOrUpdate = [
                    'dietician_id' => (int) $inputs['dietician_id'],
                    'availability_date' => date('Y-m-d', strtotime($date)),
                    'created_by' => (int) $inputs['created_by'],
                    'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                    'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                    'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                    'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                    'carry_forward_availability_days' => ($inputs['carry_forward_availability'] == 1) ? (int) $inputs['carry_forward_availability_days'] : 0
                ];
                $save[] = $availability->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/availability.availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/availability.availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/availability.availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/availability.availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Availability $availability
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $availability) {
        try {
            $save = [];
            $availabilityInsertOrUpdate = [
                'updated_by' => (int) $inputs['updated_by'],
                'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                'carry_forward_availability_days' => ($inputs['carry_forward_availability'] == 1) ? (int) $inputs['carry_forward_availability_days'] : 0
            ];
            if ($inputs['carry_forward_availability'] != 1) {
                foreach ($availabilityInsertOrUpdate as $key => $value) {
                    if (isset($availability->$key)) {
                        $availability->$key = $value;
                    }
                }
                $save[] = $availability->save();
            } else {
                $endDate = date('Y-m-d', strtotime('+14 Days'));
                for ($i = 0; $i <= $inputs['carry_forward_availability_days']; $i++) {
                    $availabilityDate = date('Y-m-d', strtotime($availability->availability_date.' +' . $i . ' Days'));
                    if ($availabilityDate >= date('Y-m-d', strtotime($availability->availability_date)) && $availabilityDate <= $endDate) {
                        $availabilityNew = new $this->model;
                        $whereClause = [
                            'dietician_id' => $availability->dietician_id,
                            'availability_date' => $availabilityDate
                        ];
                        $availabilityInsertOrUpdate ['dietician_id'] = (int) $availability->dietician_id;
                        $availabilityInsertOrUpdate ['availability_date'] = $availabilityDate;
                        $availabilityInsertOrUpdate ['created_by'] = (int) $inputs['updated_by'];
                        $save[] = $availabilityNew->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                    }
                }
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/availability.availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/availability.availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/availability.availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/availability.availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            return $response;
        }
    }

    /**
     * Delete actions on activity types
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {
            $resultStatus = false;
            $id = $inputs['ids'];
            $availability = Availability::find($id);
            if (!empty($availability)) {
                $availability->delete();
                $resultStatus = true;
            }
            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/availability.availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/availability.availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
    
    public function checkSessionTime($params){
//        print_r($params['time']);
    }

}
