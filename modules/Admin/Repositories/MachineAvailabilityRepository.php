<?php

/**
 * The repository class for managing machine availability specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MachineAvailability;
use Modules\Admin\Models\Machine;
use Modules\Admin\Models\Center;
use Exception;
use Route;
use Log;
use DB;
use PDO;
use Cache;
use Auth;

class MachineAvailabilityRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new MachineAvailabilityRepository instance.
     *
     * @param  Modules\Admin\Models\MachineAvailability $machineAvailability
     * @return void
     */
    public function __construct(MachineAvailability $machineAvailability) {
        $this->model = $machineAvailability;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $user_id = (int) $params['user_id'];
        // dd($user_id);
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $centerSearch = $params['center_id'];
        $machineSearch = $params['machine_id'];
        DB::setFetchMode(PDO::FETCH_ASSOC);
        // $request_details = [];
        // if ($params['date'] != '') {
        if (($params['from_date'] != '') && ($params['to_date'] == '')) {
            $date = date('Y-m-d', strtotime($params['from_date']));
            $s_date = "machine_availability.availability_date >= '" . $date . "'";
        } elseif (($params['from_date'] != '') && ($params['to_date'] != '')) {
            //$date = date('Y-m-d', strtotime($params['date']));
            $date = date('Y-m-d', strtotime($params['from_date']));
            $date1 = date('Y-m-d', strtotime($params['to_date']));
            $s_date = "machine_availability.availability_date BETWEEN '" . $date . "' AND '" . $date1 . "'";
        } else {
            // $s_date = $date;
            $s_date = "machine_availability.availability_date >= '" . $date . "'";
        }

        if (isset($centerSearch) && !empty($centerSearch)) {
            $center = " AND machine_availability.center_id = '$centerSearch'";
        } else {
            $center = "";
        }

        if (isset($machineSearch) && !empty($machineSearch)) {
            $machine = " AND machine_availability.machine_id = '$machineSearch'";
        } else {
            $machine = "";
        }

//        $request_details = DB::select('select machine_availability.*,machines.name as mname,vlcc_centers.center_name as cname from machine_availability INNER JOIN machines ON machine_availability.machine_id = machines.id INNER JOIN vlcc_centers ON machine_availability.center_id = vlcc_centers.id where ' . $s_date . '' . $center . '' . $machine . ' order by machines.name ASC, machine_availability.availability_date ASC');
//
        $request_details = DB::select('select machine_availability.*,machines.name as mname,vlcc_centers.center_name as cname from machine_availability INNER JOIN machines ON machine_availability.machine_id = machines.id INNER JOIN vlcc_centers ON machine_availability.center_id = vlcc_centers.id INNER JOIN admin_centers ON vlcc_centers.id = admin_centers.center_id where `admin_centers`.`user_id` = ' . $user_id . ' AND ' . $s_date . '' . $center . '' . $machine . ' order by machines.name ASC, machine_availability.availability_date ASC');

        DB::setFetchMode(PDO::FETCH_CLASS);

        $response = collect($request_details);
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
            $machineAvailability = new $this->model;
            $machineAvailability->machineCenter()->associate(Center::find($inputs['center_id']));
            $machineAvailability->machine()->associate(Machine::find($inputs['machine_id']));
            $availableDates = explode(',', $inputs['availability_date']);
            foreach ($availableDates as $date) {
                if (isset($inputs['carry_forward_availability'])) {
                    (isset($inputs['carry_forward_availability_days']) && $inputs['carry_forward_availability_days'] != '') ? $days = $inputs['carry_forward_availability_days'] : $days = 0;
                    for ($i = 0, $k = $days; $i <= $days, $k >= 0; $i++, $k--) {

                        $availabilityDate = date('Y-m-d', strtotime($date . ' +' . $i . ' Days'));
                        $machineAvailabilityNew1 = new $this->model;
                        $whereClause = [
                            'center_id' => (int) $inputs['center_id'],
                            'machine_id' => (int) $inputs['machine_id'],
                            'availability_date' => $availabilityDate
                        ];
                        $availabilityInsertOrUpdate = [
                            'center_id' => (int) $inputs['center_id'],
                            'machine_id' => (int) $inputs['machine_id'],
                            'availability_date' => $availabilityDate,
                            'created_by' => (int) $inputs['created_by'],
                            'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                            'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                            'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                            'carry_forward_availability_days' => $k
                        ];
                        $save[] = $machineAvailabilityNew1->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                    }
                } else {
                    $machineAvailability = new $this->model;
                    $whereClause = [
                        'center_id' => (int) $inputs['center_id'],
                        'machine_id' => (int) $inputs['machine_id'],
                        'availability_date' => date('Y-m-d', strtotime($date))
                    ];
                    $availabilityInsertOrUpdate = [
                        'center_id' => (int) $inputs['center_id'],
                        'machine_id' => (int) $inputs['machine_id'],
                        'availability_date' => date('Y-m-d', strtotime($date)),
                        'created_by' => (int) $inputs['created_by'],
                        'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                        'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                    ];
                    $save[] = $machineAvailability->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                }
            }//foreach

            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-availability.machine-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/machine-availability.machine-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\MachineAvailability $machineAvailability
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $machineAvailability) {
        try {
            $machineAvailability->machineCenter()->associate(Center::find($inputs['center_id']));
            $machineAvailability->machine()->associate(Machine::find($inputs['machine_id']));
            $save = [];
            $machineAvailabilityInsertOrUpdate = [
                'center_id' => (int) $inputs['center_id'],
                'machine_id' => (int) $inputs['machine_id'],
                'updated_by' => (int) $inputs['updated_by'],
                'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                // 'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                    // 'carry_forward_availability_days' => ($inputs['carry_forward_availability'] == 1) ? (int) $inputs['carry_forward_availability_days'] : 0
            ];
            if ($inputs['carry_forward_availability'] != 1) {
                foreach ($machineAvailabilityInsertOrUpdate as $key => $value) {
                    if (isset($machineAvailability->$key)) {
                        $machineAvailability->$key = $value;
                    }
                }
                $save[] = $machineAvailability->save();
            } else {

                // $endDate = date('Y-m-d', strtotime('+30 Days'));
                //  for ($i = 0; $i <= $inputs['carry_forward_availability_days']; $i++) {
                for ($i = 0, $k = $inputs['carry_forward_availability_days']; $i <= $inputs['carry_forward_availability_days'], $k >= 0; $i++, $k--) {
                    $availabilityDate = date('Y-m-d', strtotime($machineAvailability->availability_date . ' +' . $i . ' Days'));
                    // if ($availabilityDate >= date('Y-m-d', strtotime($availability->availability_date)) && $availabilityDate <= $endDate) {
                    if ($availabilityDate >= date('Y-m-d', strtotime($machineAvailability->availability_date))) {
                        $machineAvailabilityNew = new $this->model;
                        $whereClause = [
                            //'dietician_id' => $availability->dietician_id,
                            'center_id' => $machineAvailability->center_id,
                            'machine_id' => $machineAvailability->machine_id,
                            'availability_date' => $availabilityDate
                        ];
                        //$availabilityInsertOrUpdate ['dietician_id'] = (int) $availability->dietician_id;
                        $machineAvailabilityInsertOrUpdate ['center_id'] = (int) $machineAvailability->center_id;
                        $machineAvailabilityInsertOrUpdate ['machine_id'] = (int) $machineAvailability->machine_id;
                        $machineAvailabilityInsertOrUpdate ['availability_date'] = $availabilityDate;
                        $machineAvailabilityInsertOrUpdate ['created_by'] = (int) $inputs['updated_by'];
                        $machineAvailabilityInsertOrUpdate ['carry_forward_availability_days'] = $k;
                        $save[] = $machineAvailabilityNew->updateOrCreate($whereClause, $machineAvailabilityInsertOrUpdate);
                    }
                }
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-availability.machine-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine-availability.machine-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
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
            $machineAvailability = MachineAvailability::find($id);
            if (!empty($machineAvailability)) {
                $machineAvailability->delete();
                $resultStatus = true;
            }
            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function getGroupActionData() {
        if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete))) {
            return ['' => 'Delete'];
        } /* else {
          return ['' => 'Select'];
          } */
    }

    /**
     * Group actions on Machine Availability
     *
     * @param  int  $status
     * @return int
     */
    public function groupAction($inputs) {
        if (empty($inputs['action'])) {
            return false;
        }
        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "delete":
                $machineAvailabilityIds = explode(',', $inputs['ids']);
                foreach ($machineAvailabilityIds as $machineAvailabilityId) {
                    $id = (int) $machineAvailabilityId;
                    $machineAvailabilityIdDel = MachineAvailability::find($id);
                    if (!empty($machineAvailabilityIdDel)) {
                        $machineAvailabilityIdDel->delete();
                        $resultStatus = true;
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

}
