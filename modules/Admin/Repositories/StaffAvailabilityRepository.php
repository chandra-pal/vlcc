<?php

/**
 * The repository class for managing staff availability specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\StaffAvailability;
use Modules\Admin\Models\User;
use Modules\Admin\Models\Center;
use Exception;
use Route;
use Log;
use DB;
use PDO;
use Cache;
use Auth;

class StaffAvailabilityRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new StaffAvailabilityRepository instance.
     *
     * @param  Modules\Admin\Models\StaffAvailability $staffAvailability
     * @return void
     */
    public function __construct(StaffAvailability $staffAvailability) {
        $this->model = $staffAvailability;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $user_id = (int) $params['user_id'];
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $centerSearch = $params['center_id'];
        $staffSearch = $params['staff_id'];
        DB::setFetchMode(PDO::FETCH_ASSOC);
        // $request_details = [];
        // if ($params['date'] != '') {
        if (($params['from_date'] != '') && ($params['to_date'] == '')) {
            $date = date('Y-m-d', strtotime($params['from_date']));
            $s_date = "staff_availability.availability_date >= '" . $date . "'";
        } elseif (($params['from_date'] != '') && ($params['to_date'] != '')) {
            //$date = date('Y-m-d', strtotime($params['date']));
            $date = date('Y-m-d', strtotime($params['from_date']));
            $date1 = date('Y-m-d', strtotime($params['to_date']));
            $s_date = "staff_availability.availability_date BETWEEN '" . $date . "' AND '" . $date1 . "'";
        } else {
            // $s_date = $date;
            $s_date = "staff_availability.availability_date >= '" . $date . "'";
        }

        if (isset($centerSearch) && !empty($centerSearch)) {
            $center = " AND staff_availability.center_id = '$centerSearch'";
        } else {
            $center = "";
        }

        if (isset($staffSearch) && !empty($staffSearch)) {
            $staff = " AND staff_availability.staff_id = '$staffSearch'";
        } else {
            $staff = "";
        }

//        $request_details = DB::select('select staff_availability.*,admins.first_name as sname,vlcc_centers.center_name as scname from staff_availability INNER JOIN admins ON staff_availability.staff_id = admins.id INNER JOIN vlcc_centers ON staff_availability.center_id = vlcc_centers.id where ' . $s_date . '' . $center . '' . $staff .' order by admins.first_name ASC,staff_availability.availability_date ASC');

        $request_details = DB::select('select staff_availability.*,admins.first_name as sname,vlcc_centers.center_name as scname from staff_availability INNER JOIN admins ON staff_availability.staff_id = admins.id INNER JOIN vlcc_centers ON staff_availability.center_id = vlcc_centers.id INNER JOIN admin_centers ON vlcc_centers.id = admin_centers.center_id where `admin_centers`.`user_id` = ' . $user_id . ' AND ' . $s_date . '' . $center . '' . $staff . ' order by admins.first_name ASC,staff_availability.availability_date ASC');

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
            $staffAvailability = new $this->model;
            $staffAvailability->staffCenter()->associate(Center::find($inputs['center_id']));
            $staffAvailability->availabilityDietianStaff()->associate(User::find($inputs['staff_id']));
            $availableDates = explode(',', $inputs['availability_date']);
            // $carryForwardAvailability = $inputs['carry_forward_availability'];
            foreach ($availableDates as $date) {
                if (isset($inputs['break_time']) && $inputs['break_time'] != '') {
                    $breakTime = $inputs['break_time'];
                } else {
                    $breakTime = '';
                }
                if (isset($inputs['carry_forward_availability'])) {
                    (isset($inputs['carry_forward_availability_days']) && $inputs['carry_forward_availability_days'] != '') ? $days = $inputs['carry_forward_availability_days'] : $days = 0;
                    for ($i = 0, $k = $days; $i <= $days, $k >= 0; $i++, $k--) {

                        $availabilityDate = date('Y-m-d', strtotime($date . ' +' . $i . ' Days'));
                        $staffAvailabilityNew1 = new $this->model;
                        $whereClause = [
                            'center_id' => (int) $inputs['center_id'],
                            'staff_id' => (int) $inputs['staff_id'],
                            'availability_date' => $availabilityDate
                        ];
                        $availabilityInsertOrUpdate = [
                            'center_id' => (int) $inputs['center_id'],
                            'staff_id' => (int) $inputs['staff_id'],
                            'availability_date' => $availabilityDate,
                            'created_by' => (int) $inputs['created_by'],
                            'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                            'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                            //'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                            // 'break_time' => date('H:i:s', strtotime($breakTime)),
                            'break_time' => $breakTime,
                            'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                            'carry_forward_availability_days' => $k
                        ];
                        $save[] = $staffAvailabilityNew1->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                    }
                } else {
                    $staffAvailability = new $this->model;
                    $whereClause = [
                        'center_id' => (int) $inputs['center_id'],
                        'staff_id' => (int) $inputs['staff_id'],
                        'availability_date' => date('Y-m-d', strtotime($date))
                    ];
                    $availabilityInsertOrUpdate = [
                        'center_id' => (int) $inputs['center_id'],
                        'staff_id' => (int) $inputs['staff_id'],
                        'availability_date' => date('Y-m-d', strtotime($date)),
                        'created_by' => (int) $inputs['created_by'],
                        'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                        'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                        //'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                        // 'break_time' => date('H:i:s', strtotime($breakTime)),
                        'break_time' => $breakTime,
                    ];
                    $save[] = $staffAvailability->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                }
            }//foreach

            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/staff-availability.staff-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/staff-availability.staff-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\StaffAvailability $staffAvailability
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $staffAvailability) {
        try {
            $staffAvailability->staffCenter()->associate(Center::find($inputs['center_id']));
            $staffAvailability->availabilityDietianStaff()->associate(User::find($inputs['staff_id']));
            $save = [];
            $staffAvailabilityInsertOrUpdate = [
                'center_id' => (int) $inputs['center_id'],
                'staff_id' => (int) $inputs['staff_id'],
                'updated_by' => (int) $inputs['updated_by'],
                'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                    // 'carry_forward_availability_days' => ($inputs['carry_forward_availability'] == 1) ? (int) $inputs['carry_forward_availability_days'] : 0
            ];
            if ($inputs['carry_forward_availability'] != 1) {
                foreach ($staffAvailabilityInsertOrUpdate as $key => $value) {
                    if (isset($staffAvailability->$key)) {
                        $staffAvailability->$key = $value;
                    }
                }
                $save[] = $staffAvailability->save();
            } else {

                // $endDate = date('Y-m-d', strtotime('+30 Days'));
                //  for ($i = 0; $i <= $inputs['carry_forward_availability_days']; $i++) {
                for ($i = 0, $k = $inputs['carry_forward_availability_days']; $i <= $inputs['carry_forward_availability_days'], $k >= 0; $i++, $k--) {
                    $availabilityDate = date('Y-m-d', strtotime($staffAvailability->availability_date . ' +' . $i . ' Days'));
                    // if ($availabilityDate >= date('Y-m-d', strtotime($availability->availability_date)) && $availabilityDate <= $endDate) {
                    if ($availabilityDate >= date('Y-m-d', strtotime($staffAvailability->availability_date))) {
                        $staffAvailabilityNew = new $this->model;
                        $whereClause = [
                            'center_id' => $staffAvailability->center_id,
                            'staff_id' => $staffAvailability->staff_id,
                            'availability_date' => $availabilityDate
                        ];
                        $staffAvailabilityInsertOrUpdate ['center_id'] = (int) $staffAvailability->center_id;
                        $staffAvailabilityInsertOrUpdate ['staff_id'] = (int) $staffAvailability->staff_id;
                        $staffAvailabilityInsertOrUpdate ['availability_date'] = $availabilityDate;
                        $staffAvailabilityInsertOrUpdate ['created_by'] = (int) $inputs['updated_by'];
                        $staffAvailabilityInsertOrUpdate ['carry_forward_availability_days'] = $k;
                        $save[] = $staffAvailabilityNew->updateOrCreate($whereClause, $staffAvailabilityInsertOrUpdate);
                    }
                }
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff-availability.staff-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/staff-availability.staff-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
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
            $staffAvailability = StaffAvailability::find($id);
            if (!empty($staffAvailability)) {
                $staffAvailability->delete();
                $resultStatus = true;
            }
            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

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
                $staffAvailabilityIds = explode(',', $inputs['ids']);
                foreach ($staffAvailabilityIds as $staffAvailabilityId) {
                    $id = (int) $staffAvailabilityId;
                    $staffAvailabilityIdDel = StaffAvailability::find($id);
                    if (!empty($staffAvailabilityIdDel)) {
                        $staffAvailabilityIdDel->delete();
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
