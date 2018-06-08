<?php

/**
 * The repository class for managing machine type actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MachineType;
use Cache;

class MachineTypeRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\MachineType $machineType
     * @return void
     */
    public function __construct(MachineType $machineType) {
        $this->model = $machineType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(MachineType::table())->remember($cacheKey, $this->ttlCache, function() {
            return MachineType::orderBy('machine_type')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    // To display data in machine type dropdown
    public function listMachineTypesData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(MachineType::table())->remember($cacheKey, $this->ttlCache, function() {
            return MachineType::orderBY('machine_type')->whereStatus(1)->lists('machine_type', 'id');
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
            $machineType = new $this->model;
            $allColumns = $machineType->getTableColumns($machineType->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $machineType->$key = $value;
                }
            }
           
            $machineType->machine_type = ucfirst(strtolower($inputs['machine_type']));
            $save = $machineType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/machine-type.machine-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-type.machine-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-type.machine-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-type.machine-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a machine.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\MachineType $machineType
     * @return $result array with status and message elements
     */
    public function update($inputs, $machineType) {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($machineType->$key)) {
                    $machineType->$key = $value;
                }
            }

            $machineType->machine_type = ucfirst(strtolower($inputs['machine_type']));
            $save = $machineType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/machine-type.machine-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-type.machine-type')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-type.machine-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-type.machine-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
