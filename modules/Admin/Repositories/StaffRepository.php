<?php

/**
 * The repository class for managing staff actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Staff;
use Cache;

class StaffRepository extends BaseRepository {

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\Staff $staff
     * @return void
     */
    public function __construct(Staff $staff) {
        $this->model = $staff;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($request, $params = []) {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Staff::table())->remember($cacheKey, $this->ttlCache, function() {
            return Staff::orderBy('first_name')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listStaffData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Staff::table())->remember($cacheKey, $this->ttlCache, function() {
            return Staff::orderBY('first_name')->lists('first_name', 'id');
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function store($inputs) {
        try {
            $staff = new $this->model;
            $allColumns = $staff->getTableColumns($staff->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $staff->$key = $value;
                }
            }

            $save = $staff->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/staff.staff')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/staff.staff')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/staff.staff')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/staff.staff')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update  staff.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Staff $staff
     * @return $result array with status and message elements
     */
    public function update($inputs, $staff) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($staff->$key)) {
                    $staff->$key = $value;
                }
            }

            $save = $staff->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/staff.staff')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff.staff')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff.staff')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff.staff')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
