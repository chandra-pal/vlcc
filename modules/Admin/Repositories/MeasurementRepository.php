<?php

/**
 * The repository class for managing measurement specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Measurement;
use Exception;
use Route;
use Log;
use Cache;

class MeasurementRepository extends BaseRepository {

    /**
     * Create a new MeasurementRepository instance.
     *
     * @param  Modules\Admin\Models\Measurement $model
     * @return void
     */
    public function __construct(Measurement $measurement) {
        $this->model = $measurement;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Measurement::table())->remember($cacheKey, $this->ttlCache, function() {
            return Measurement::select([
                        'id', 'title', 'meaning', 'status'
                    ])->orderBy('id')->get();
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
        $response = Cache::tags(Measurement::table())->remember($cacheKey, $this->ttlCache, function() {
            return Measurement::orderBY('id')->lists('id', 'title', 'meaning', 'status');
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listCategoryData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Measurement::table())->remember($cacheKey, $this->ttlCache, function() {
            return Measurement::orderBY('id')->lists('id', 'title', 'meaning', 'status');
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
            $measurement = new $this->model;

            $allColumns = $measurement->getTableColumns($measurement->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $measurement->$key = $value;
                }
            }
            $measurement->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $save = $measurement->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/measurement.measurement')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/measurement.measurement')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/measurement.measurement')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/measurement.measurement')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an measurement.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Measurement $measurement
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $measurement) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($measurement->$key)) {
                    $measurement->$key = $value;
                }
            }
            $measurement->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $save = $measurement->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/measurement.measurement')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/measurement.measurement')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/measurement.measurement')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/measurement.measurement')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on measurement
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $measurement = Measurement::find($id);
            if (!empty($measurement)) {
                $measurement->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/measurement.measurement')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/measurement.measurement')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
