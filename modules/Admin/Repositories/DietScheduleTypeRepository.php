<?php

/**
 * The repository class for managing Diet Schedule Type specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\DietScheduleType;
use Exception;
use Route;
use Log;
use Cache;

class DietScheduleTypeRepository extends BaseRepository {

    /**
     * Create a new DietScheduleTypeRepository instance.
     *
     * @param  Modules\Admin\Models\DietScheduleType $model
     * @return void
     */
    public function __construct(DietScheduleType $model) {
        $this->model = $model;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        Cache::flush();
        $response = Cache::tags(DietScheduleType::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietScheduleType::select([
                        'id', 'schedule_name', 'start_time', 'end_time', 'status'
                    ])->orderBy('id')->get();
        });

        return $response;
    }

    public function listScheduleTypes() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(DietScheduleType::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietScheduleType::orderBY('id')->lists('schedule_name', 'id');
        });

        return $response;
    }

    public function getLastScheduleType() {
        $response = DietScheduleType::select('id')->orderBY('id', 'DESC')->first();
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
            $dietScheduleType = new $this->model;

            $allColumns = $dietScheduleType->getTableColumns($dietScheduleType->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $dietScheduleType->$key = $value;
                }
            }
            $dietScheduleType->schedule_name = ucfirst($inputs['schedule_name']);
            $dietScheduleType->start_time = date("H:i:s", strtotime($inputs['start_time']));
            $dietScheduleType->end_time = date("H:i:s", strtotime($inputs['end_time']));
            $dietScheduleType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $dietScheduleType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an Diet schedule.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\DietScheduleType $dietScheduleType
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $dietScheduleType) {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($dietScheduleType->$key)) {
                    $dietScheduleType->$key = $value;
                }
            }
            $dietScheduleType->schedule_name = ucfirst($inputs['schedule_name']);
            $dietScheduleType->start_time = date("H:i:s", strtotime($inputs['start_time']));
            $dietScheduleType->end_time = date("H:i:s", strtotime($inputs['end_time']));
            $dietScheduleType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $dietScheduleType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on diet schedule
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $dietScheduleType = DietScheduleType::find($id);
            if (!empty($dietScheduleType)) {
                $dietScheduleType->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
