<?php

/**
 * The repository class for managing food specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Food;
use Modules\Admin\Models\Room;
use Modules\Admin\Models\Machine;
use Modules\Admin\Models\RoomAvailability;
use Modules\Admin\Models\MachineAvailability;
use Modules\Admin\Models\StaffAvailability;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;

class SessionResourcesRepository extends BaseRepository {

    /**
     * Create a new FoodRepository instance.
     *
     * @param  Modules\Admin\Models\Food $model
     * @return void
     */
    public function __construct(Food $food, Room $room, RoomAvailability $roomAvailability) {
        $this->model = $food;
        $this->roomModel = $room;
        $this->roomAvailability = $roomAvailability;
    }

    public function fetchResources($params) {
        if ($params["flag"] == 1) {

            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = [];
            $request_details = DB::table('admin_centers')
                    ->join('admins', 'admin_centers.user_id', '=', 'admins.id')
                    ->select('admins.first_name', 'admins.last_name', 'admins.id')
                    ->where('admin_centers.center_id', '=', $params["center_id"])
                    ->where('admins.status', "=", 1)
                    //->whereIn('admins.user_type_id', [4, 5, 10])
                    ->whereNotIn('admins.user_type_id', [1,2,3,9,11])
                    ->orderBy('admins.first_name')
                    ->get();

            return collect($request_details);
        } else if ($params["flag"] == 2) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = [];
            $request_details = DB::table('machine_centers')
                    ->join('machines', 'machine_centers.machine_id', '=', 'machines.id')
                    ->select('machines.name', 'machines.id')
                    ->where('machine_centers.center_id', '=', $params["center_id"])
                    ->where('machines.status', "=", 1)
                    ->orderBy('machines.name')
                    ->get();

            return collect($request_details);
        } else if ($params["flag"] == 3) {
            return Room::where('center_id', '=', $params["center_id"])
                            ->where('status', "=", 1)
                            ->get();
        }
    }

    public function getResourcesAvailability($params) {
        if ($params["flag"] == 1) {
            return StaffAvailability::where('center_id', '=', $params["center_id"])
                            //->where('availability_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                            ->get();
        } else if ($params["flag"] == 2) {
            return MachineAvailability::where('center_id', '=', $params["center_id"])
                            //->where('availability_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                            ->get();
        } else if ($params["flag"] == 3) {
            return RoomAvailability::where('center_id', '=', $params["center_id"])
                            //->where('availability_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                            ->get();
        }
    }

    public function getBookedResources($params) {
        if ($params["flag"] == 1) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = DB::table('admin_centers AS ac')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'ac.user_id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->select('ac.*','msb.session_date','msb.start_time', 'msb.end_time','members.first_name','members.mobile_number')
                //->where('ma.availability_date', '=', date('Y-m-d', strtotime($params["availability_date"])))
                ->where('ac.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '1')
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->get();

            return collect($request_details);
        } else if ($params["flag"] == 2) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = DB::table('machine_centers AS mc')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'mc.machine_id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->select('mc.*','msb.session_date','msb.start_time', 'msb.end_time','members.first_name','members.mobile_number')
                //->where('ma.availability_date', '=', date('Y-m-d', strtotime($params["availability_date"])))
                ->where('mc.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '2')
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->get();

            return collect($request_details);

        } else if ($params["flag"] == 3) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $request_details = DB::table('rooms')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'rooms.id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->select('rooms.*','msb.session_date','msb.start_time', 'msb.end_time','members.first_name','members.mobile_number')
                //->where('ma.availability_date', '=', date('Y-m-d', strtotime($params["availability_date"])))
                ->where('rooms.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '3')
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->get();

            return collect($request_details);
        }
    }

    public function dateWiseBookedResources($params) {
        if ($params["flag"] == 1) {
            //DB::setFetchMode(PDO::FETCH_ASSOC);
            return $request_details = DB::table('admin_centers AS ac')
                ->leftJoin('admins', 'admins.id', '=', 'ac.user_id')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'ac.user_id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->leftJoin('member_packages AS mp', 'msb.package_id', '=', 'mp.id')
                ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS staff_name'),'msb.id AS session_id',
                    DB::raw("(SELECT GROUP_CONCAT(mps.service_name) FROM member_session_bookings AS mbs_new
                                INNER JOIN member_package_services AS mps ON FIND_IN_SET(mps.id, mbs_new.service_id) > 0
                                WHERE mbs_new.id = session_id) as service_name"),
                    DB::raw("(SELECT GROUP_CONCAT(bs.service_name) FROM member_session_bookings AS mbs_new2
                                INNER JOIN beauty_services AS bs ON FIND_IN_SET(bs.id, mbs_new2.service_id) > 0
                                WHERE mbs_new2.id = session_id) as beauty_service_name"),
                    'ac.*','msb.session_date','msb.package_id','msb.start_time', 'msb.end_time', 'msb.status','members.first_name','members.mobile_number','mp.package_title')
                ->where('ac.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '1')
                ->where('msb.session_date', ">=", date('Y-m-d', strtotime($params['from_date'])))
                ->where('msb.session_date', "<=", date('Y-m-d', strtotime($params['to_date'])))
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->orderBY('msb.session_date', 'ASC');
            //->get();

            //return collect($request_details);
        } else if ($params["flag"] == 2) {

            return $request_details = DB::table('machine_centers AS mc')
                ->join('machines', 'mc.machine_id', '=', 'machines.id')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'mc.machine_id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->leftJoin('member_packages AS mp', 'msb.package_id', '=', 'mp.id')
                ->select('machines.name AS machine_name','msb.id AS session_id',
                    DB::raw("(SELECT GROUP_CONCAT(mps.service_name) FROM member_session_bookings AS mbs_new
                                INNER JOIN member_package_services AS mps ON FIND_IN_SET(mps.id, mbs_new.service_id) > 0
                                WHERE mbs_new.id = session_id) as service_name"),
                    DB::raw("(SELECT GROUP_CONCAT(bs.service_name) FROM member_session_bookings AS mbs_new2
                                INNER JOIN beauty_services AS bs ON FIND_IN_SET(bs.id, mbs_new2.service_id) > 0
                                WHERE mbs_new2.id = session_id) as beauty_service_name"),
                    'mc.*','msb.session_date','msb.package_id','msb.start_time', 'msb.end_time', 'msb.status','members.first_name','members.mobile_number','mp.package_title')
                ->where('mc.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '2')
                ->where('msb.session_date', ">=", date('Y-m-d', strtotime($params['from_date'])))
                ->where('msb.session_date', "<=", date('Y-m-d', strtotime($params['to_date'])))
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->orderBY('msb.session_date', 'ASC');

        } else if ($params["flag"] == 3) {

            return $request_details = DB::table('rooms')
                ->leftJoin('member_session_booking_resources AS msba', 'msba.resource_id', '=', 'rooms.id')
                ->leftJoin('member_session_bookings AS msb', 'msba.session_id', '=', 'msb.id')
                ->leftJoin('members', 'msb.member_id', '=', 'members.id')
                ->leftJoin('member_packages AS mp', 'msb.package_id', '=', 'mp.id')
                ->select('msb.id AS session_id',
                    DB::raw("(SELECT GROUP_CONCAT(mps.service_name) FROM member_session_bookings AS mbs_new
                                INNER JOIN member_package_services AS mps ON FIND_IN_SET(mps.id, mbs_new.service_id) > 0
                                WHERE mbs_new.id = session_id) as service_name"),
                    DB::raw("(SELECT GROUP_CONCAT(bs.service_name) FROM member_session_bookings AS mbs_new2
                                INNER JOIN beauty_services AS bs ON FIND_IN_SET(bs.id, mbs_new2.service_id) > 0
                                WHERE mbs_new2.id = session_id) as beauty_service_name"),
                    'rooms.*','msb.session_date','msb.package_id','msb.start_time', 'msb.end_time', 'msb.status','members.first_name','members.mobile_number','mp.package_title')
                ->where('rooms.center_id', "=", $params["center_id"])
                ->where('msba.resource_type', "=", '3')
                ->where('msb.session_date', ">=", date('Y-m-d', strtotime($params['from_date'])))
                ->where('msb.session_date', "<=", date('Y-m-d', strtotime($params['to_date'])))
                //->where('msb.session_date', "=", date('Y-m-d', strtotime($params["availability_date"])))
                ->where(function ($query) {
                    $query->where('msb.status', '=', '2')
                        ->orWhere('msb.status', '=', '5')
                        ->orWhere('msb.status', '=', '7');
                })
                ->orderBY('msb.session_date', 'ASC');
        }
    }
    
    //Function to fetch center details when center head logs in
    public function fetchCenterHeadCenter($loggedInUserId){
        $result = DB::select("select vlcc_centers.id,vlcc_centers.center_name from vlcc_centers left join admin_centers on vlcc_centers.id = admin_centers.center_id where admin_centers.user_id = ".$loggedInUserId);
      
        return $result;
    }

}
