<?php

/**
 * The repository class for managing beauty services actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\BeautyServices;
use Cache;

class BeautyServiceRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\BeautyServices $beautyService
     * @return void
     */
    public function __construct(BeautyServices $beautyService) {
        $this->model = $beautyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(BeautyServices::table())->remember($cacheKey, $this->ttlCache, function() {
            return BeautyServices::orderBy('service_name')->get();
        });

        return $response;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs) {
        try {
            $beautyService = new $this->model;
            $allColumns = $beautyService->getTableColumns($beautyService->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $beautyService->$key = $value;
                }
            }
           
            $beautyService->service_name = ucwords($inputs['service_name']);
            $save = $beautyService->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/beauty-service.beauty-service')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/beauty-service.beauty-service')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/beauty-service.beauty-service')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/beauty-service.beauty-service')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a beauty service.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\BeautyServices $beautyService
     * @return $result array with status and message elements
     */
    public function update($inputs, $beautyService) {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($beautyService->$key)) {
                    $beautyService->$key = $value;
                }
            }

            $beautyService->service_name = ucwords($inputs['service_name']);
            $save = $beautyService->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/beauty-service.beauty-service')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/beauty-service.beauty-service')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/beauty-service.beauty-service')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/beauty-service.beauty-service')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
