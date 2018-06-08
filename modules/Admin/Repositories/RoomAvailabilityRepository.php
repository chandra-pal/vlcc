<?php

/**
 * The repository class for managing room availability specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\RoomAvailability;
use Modules\Admin\Models\Room;
use Modules\Admin\Models\Center;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;
use Auth;

class RoomAvailabilityRepository extends BaseRepository {

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new RoomAvailabilityRepository instance.
     *
     * @param  Modules\Admin\Models\RoomAvailability $roomAvailability
     * @return void
     */
    public function __construct(RoomAvailability $roomAvailability) {
        $this->model = $roomAvailability;
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
        $roomSearch = $params['room_id'];
        DB::setFetchMode(PDO::FETCH_ASSOC);
        // $request_details = [];
        // if ($params['date'] != '') {
        if (($params['from_date'] != '') && ($params['to_date'] == '')) {
            $date = date('Y-m-d', strtotime($params['from_date']));
            $s_date = "room_availability.availability_date >= '" . $date . "'";
        } elseif (($params['from_date'] != '') && ($params['to_date'] != '')) {
            //$date = date('Y-m-d', strtotime($params['date']));
            $date = date('Y-m-d', strtotime($params['from_date']));
            $date1 = date('Y-m-d', strtotime($params['to_date']));
            $s_date = "room_availability.availability_date BETWEEN '" . $date . "' AND '" . $date1 . "'";
        } else {
            // $s_date = $date;
            $s_date = "room_availability.availability_date >= '" . $date . "'";
        }

        if (isset($centerSearch) && !empty($centerSearch)) {
            $center = " AND room_availability.center_id = '$centerSearch'";
        } else {
            $center = "";
        }

        if (isset($roomSearch) && !empty($roomSearch)) {
            $room = " AND room_availability.room_id = '$roomSearch'";
        } else {
            $room = "";
        }

//        $request_details = DB::select('select room_availability.*,rooms.name as rname,vlcc_centers.center_name as rcname from room_availability INNER JOIN rooms ON room_availability.room_id = rooms.id INNER JOIN vlcc_centers ON room_availability.center_id = vlcc_centers.id where ' . $s_date . '' . $center . '' . $room . ' order by rooms.name ASC,room_availability.availability_date ASC ');

        $request_details = DB::select('select room_availability.*,rooms.name as rname,vlcc_centers.center_name as rcname from room_availability INNER JOIN rooms ON room_availability.room_id = rooms.id INNER JOIN vlcc_centers ON room_availability.center_id = vlcc_centers.id INNER JOIN admin_centers ON vlcc_centers.id = admin_centers.center_id where `admin_centers`.`user_id` = ' . $user_id . ' AND ' . $s_date . '' . $center . '' . $room . ' order by rooms.name ASC,room_availability.availability_date ASC ');

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
            $roomAvailability = new $this->model;
            $roomAvailability->roomCenter()->associate(Center::find($inputs['center_id']));
            $roomAvailability->room()->associate(Room::find($inputs['room_id']));
            $availableDates = explode(',', $inputs['availability_date']);
            foreach ($availableDates as $date) {
                if (isset($inputs['carry_forward_availability'])) {
                    (isset($inputs['carry_forward_availability_days']) && $inputs['carry_forward_availability_days'] != '') ? $days = $inputs['carry_forward_availability_days'] : $days = 0;
                    for ($i = 0, $k = $days; $i <= $days, $k >= 0; $i++, $k--) {

                        $availabilityDate = date('Y-m-d', strtotime($date . ' +' . $i . ' Days'));
                        $roomAvailabilityNew1 = new $this->model;
                        $whereClause = [
                            'center_id' => (int) $inputs['center_id'],
                            'room_id' => (int) $inputs['room_id'],
                            'availability_date' => $availabilityDate
                        ];
                        $availabilityInsertOrUpdate = [
                            'center_id' => (int) $inputs['center_id'],
                            'room_id' => (int) $inputs['room_id'],
                            'availability_date' => $availabilityDate,
                            'created_by' => (int) $inputs['created_by'],
                            'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                            'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                            'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                            'carry_forward_availability_days' => $k
                        ];
                        $save[] = $roomAvailabilityNew1->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                    }
                } else {
                    $roomAvailability = new $this->model;
                    $whereClause = [
                        'center_id' => (int) $inputs['center_id'],
                        'room_id' => (int) $inputs['room_id'],
                        'availability_date' => date('Y-m-d', strtotime($date))
                    ];
                    $availabilityInsertOrUpdate = [
                        'center_id' => (int) $inputs['center_id'],
                        'room_id' => (int) $inputs['room_id'],
                        'availability_date' => date('Y-m-d', strtotime($date)),
                        'created_by' => (int) $inputs['created_by'],
                        'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                        'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                    ];
                    $save[] = $roomAvailability->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                }
            }//foreach

            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/room-availability.room-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/room-availability.room-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/room-availability.room-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/room-availability.room-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\RoomAvailability $roomAvailability
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $roomAvailability) {
        try {
            $roomAvailability->roomCenter()->associate(Center::find($inputs['center_id']));
            $roomAvailability->room()->associate(Room::find($inputs['room_id']));
            $save = [];
            $roomAvailabilityInsertOrUpdate = [
                'center_id' => (int) $inputs['center_id'],
                'room_id' => (int) $inputs['room_id'],
                'updated_by' => (int) $inputs['updated_by'],
                'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                // 'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                'carry_forward_availability' => (int) $inputs['carry_forward_availability'],
                    // 'carry_forward_availability_days' => ($inputs['carry_forward_availability'] == 1) ? (int) $inputs['carry_forward_availability_days'] : 0
            ];
            if ($inputs['carry_forward_availability'] != 1) {
                foreach ($roomAvailabilityInsertOrUpdate as $key => $value) {
                    if (isset($roomAvailability->$key)) {
                        $roomAvailability->$key = $value;
                    }
                }
                $save[] = $roomAvailability->save();
            } else {

                // $endDate = date('Y-m-d', strtotime('+30 Days'));
                //  for ($i = 0; $i <= $inputs['carry_forward_availability_days']; $i++) {
                for ($i = 0, $k = $inputs['carry_forward_availability_days']; $i <= $inputs['carry_forward_availability_days'], $k >= 0; $i++, $k--) {
                    $availabilityDate = date('Y-m-d', strtotime($roomAvailability->availability_date . ' +' . $i . ' Days'));
                    // if ($availabilityDate >= date('Y-m-d', strtotime($availability->availability_date)) && $availabilityDate <= $endDate) {
                    if ($availabilityDate >= date('Y-m-d', strtotime($roomAvailability->availability_date))) {
                        $roomAvailabilityNew = new $this->model;
                        $whereClause = [
                            //'dietician_id' => $availability->dietician_id,
                            'center_id' => $roomAvailability->center_id,
                            'room_id' => $roomAvailability->room_id,
                            'availability_date' => $availabilityDate
                        ];
                        //$availabilityInsertOrUpdate ['dietician_id'] = (int) $availability->dietician_id;
                        $roomAvailabilityInsertOrUpdate ['center_id'] = (int) $roomAvailability->center_id;
                        $roomAvailabilityInsertOrUpdate ['room_id'] = (int) $roomAvailability->room_id;
                        $roomAvailabilityInsertOrUpdate ['availability_date'] = $availabilityDate;
                        $roomAvailabilityInsertOrUpdate ['created_by'] = (int) $inputs['updated_by'];
                        $roomAvailabilityInsertOrUpdate ['carry_forward_availability_days'] = $k;
                        $save[] = $roomAvailabilityNew->updateOrCreate($whereClause, $roomAvailabilityInsertOrUpdate);
                    }
                }
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/room-availability.room-availability')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/room-availability.room-availability')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/room-availability.room-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/room-availability.room-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
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
            $roomAvailability = RoomAvailability::find($id);
            if (!empty($roomAvailability)) {
                $roomAvailability->delete();
                $resultStatus = true;
            }
            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/room-availability.room-availability')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/room-availability.room-availability')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

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
     * Group actions on Room Availability
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
                $roomAvailabilityIds = explode(',', $inputs['ids']);
                foreach ($roomAvailabilityIds as $roomAvailabilityId) {
                    $id = (int) $roomAvailabilityId;
                    $roomAvailabilityIdDel = RoomAvailability::find($id);
                    if (!empty($roomAvailabilityIdDel)) {
                        $roomAvailabilityIdDel->delete();
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
