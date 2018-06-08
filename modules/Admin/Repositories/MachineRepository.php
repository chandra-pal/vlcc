<?php

/**
 * The repository class for managing machine actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Machine;
use Modules\Admin\Models\MachineType;
use Modules\Admin\Models\Center;
use Cache;
use DB;
use PDO;

class MachineRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\Machine $machine
     * @return void
     */
    public function __construct(Machine $machine) {
        $this->model = $machine;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {

        $user_id = (int) $params['user_id'];
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = [];
        if (isset($user_id) && $user_id != '') {
//            $request_details = DB::select('select group_concat(vlcc_centers.center_name) as center_id,`machines`.`id`, `machines`.`name`, `machines`.`description`, `machines`.`status`, `vlcc_centers`.`center_name` from `machines` inner join `machine_centers` on `machines`.`id` = `machine_centers`.`machine_id` inner join `admin_centers` on `machine_centers`.`center_id` = `admin_centers`.`center_id` inner join `vlcc_centers` on `admin_centers`.`center_id` = `vlcc_centers`.`id` where `admin_centers`.`user_id` =' . $user_id . '  group by machine_centers.machine_id order by `machines`.`name` asc ');
            $request_details = DB::select('select group_concat(vlcc_centers.center_name) as center_id,`machines`.`id`, `machines`.`name`, `machines`.`description`, `machines`.`status`,`machines`.`machine_type_id`, `vlcc_centers`.`center_name` ,`machine_types`.`machine_type` from `machine_types` inner join `machines` on  `machine_types`.`id` = `machines`.`machine_type_id`
inner join `machine_centers` on `machines`.`id` = `machine_centers`.`machine_id` inner join `admin_centers` on `machine_centers`.`center_id` = `admin_centers`.`center_id` inner join `vlcc_centers` on `admin_centers`.`center_id` = `vlcc_centers`.`id`
where `admin_centers`.`user_id` = ' . $user_id . ' group by machine_centers.machine_id order by `machines`.`name` asc');
        }

        DB::setFetchMode(PDO::FETCH_CLASS);
        $machinesData = collect($request_details);
        return $machinesData;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listMachineData($centerId) {
        $centerId = (int) $centerId;
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = [];
        if (isset($centerId) && $centerId != '') {
            $request_details = DB::table('machine_centers')
                    ->join('machines', 'machine_centers.machine_id', '=', 'machines.id')
                    ->select('machines.name', 'machines.id')
                    ->where('machine_centers.center_id', '=', $centerId)
                    ->where('machines.status', '=', 1)
                    ->orderBy('machines.name')
                    ->get();
                    
        }
        $machinesData = collect($request_details)->toArray();
        $machines = array_column($machinesData, 'name', 'id');
        return $machines;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs) {
        $response = [];
        try {
            $machine = new $this->model;
            $allColumns = $machine->getTableColumns($machine->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $machine->$key = $value;
                }
            }

            $machine->machinetype()->associate(MachineType::find($inputs['machine_type_id']));

            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = DB::select("Select machine_type from machine_types where id = " . $inputs['machine_type_id']);
            DB::setFetchMode(PDO::FETCH_CLASS);
            $machineType = collect($request_details)->toArray();

            $machine->name = $machineType[0]['machine_type']."-".ucfirst($inputs['name']);
            $save = $machine->save();

            //inserting records in machine_centers table
            $centersIds = $inputs['center_id'];
            $machine->attachCenters($centersIds);
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/machine.machine')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/machine.machine')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/machine.machine')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/machine.machine')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            return $response;
        }
    }

    /**
     * Update a machine.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Machine $machine
     * @return $result array with status and message elements
     */
    public function update($inputs, $machine) {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($machine->$key)) {
                    $machine->$key = $value;
                }
            }
            $machine->machinetype()->associate(MachineType::find($inputs['machine_type_id']));
            
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = DB::select("Select machine_type from machine_types where id = " . $inputs['machine_type_id']);
            DB::setFetchMode(PDO::FETCH_CLASS);
            $machineType = collect($request_details)->toArray();
            $machine->name = $machineType[0]['machine_type']."-".ucfirst($inputs['name']);
            $save = $machine->save();

            //inserting records in machine_centers table
            if (!empty($inputs['center_id'])) {
                $centers = $inputs['center_id'];
                $existingData = $this->getCentersIdsByMachine($machine->id);
                $addCenterIds = array_diff($centers, $existingData);
                if (!empty($addCenterIds)) {
                    $machine->attachCenters($addCenterIds);
                    Cache::tags(Center::table())->flush();
                }

                //deleting records from machine_centers table
                $deleteCenterIds = array_diff($existingData, $centers);
                if (!empty($deleteCenterIds)) {
                    $machine->detachCenters($deleteCenterIds);
                    Cache::tags(Center::table())->flush();
                }
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/machine.machine')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/machine.machine')]);
            }
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine.machine')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine.machine')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
        }
        return $response;
    }

    /**
     * Group actions on Users
     *
     * @param  array $inputs
     * @return int
     */
    public function groupAction($inputs) {
        if (empty($inputs['action'])) {
            return false;
        }
        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "update":
                $machineIds = explode(',', $inputs['ids']);
                foreach ($machineIds as $machineId) {
                    $id = (int) $machineId;
                    $machine = Machine::find($id);
                    if (!empty($machine)) {
                        if ($inputs['field'] === 'status') {
                            $inputPass['status'] = (bool) $inputs['value'];
                            $this->updateStatus($inputPass, $machine);
                            $resultStatus = true;
                        }
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

    /**
     * Update machine status.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Machine $machine
     * @return void
     */
    public function updateStatus($inputs, $machine) {
        if (isset($inputs['status'])) {
            $machine->status = $inputs['status'] == 'true';
        }

        $this->update($inputs, $machine);
    }

    /**
     * Fetch exisitng centers from machine_centers
     * @param $machine_id
     * @return $response of all exisiting Centers with same $machine_id
     */
    public function getCentersIdsByMachine($machine_id) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($machine_id);
        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() use ($machine_id) {
            return Machine::find($machine_id)->center()->lists('center_id')->toArray();
        });

        return $response;
    }

}
