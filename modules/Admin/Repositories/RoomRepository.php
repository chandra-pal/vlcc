<?php

/**
 * The repository class for managing room actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Room;
use Modules\Admin\Models\Center;
use Cache;
use DB;
use PDO;

class RoomRepository extends BaseRepository {

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\Room $room
     * @return void
     */
    public function __construct(Room $room) {
        $this->model = $room;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = [];
        $centerIds = DB::select("Select center_id from admin_centers where user_id = " . $params['user_id'] . "");
        if (isset($centerIds[0]) && isset($centerIds[0]['center_id']) && $centerIds[0]['center_id'] != '') {
            $request_details = DB::table('rooms')
                    ->join('vlcc_centers', 'rooms.center_id', '=', 'vlcc_centers.id')
                    ->select('rooms.*', 'vlcc_centers.center_name as cname')
                    ->whereIn('rooms.center_id', $centerIds)
                    ->orderBy('rooms.name')
                    ->get();
        }
        $response = collect($request_details);
        return $response;
    }

    /**
     * Display a listing of the resource.(which rooms are allocated to a particular center selected from center dropdown list)
     *
     * @return Response
     */
    public function listRoomData($centerId) {
        $centerId = (int) $centerId;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($centerId));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Room::table())->remember($cacheKey, $this->ttlCache, function() use($centerId) {
            return Room::whereCenterId($centerId)->whereStatus(1)->orderBY('name')->lists('name', 'id');
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
            $room = new $this->model;
            $allColumns = $room->getTableColumns($room->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $room->$key = $value;
                }
            }
            
            $room->center()->associate(Center::find($inputs['center_id']));
            $room->name = ucfirst(strtolower($inputs['name']));
            $save = $room->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/room.room')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/room.room')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/room.room')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/room.room')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a room.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Room $room
     * @return $result array with status and message elements
     */
    public function update($inputs, $room) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($room->$key)) {
                    $room->$key = $value;
                }
            }
            
            $room->name = ucfirst(strtolower($inputs['name']));
            $room->center()->associate(Center::find($inputs['center_id']));
            $save = $room->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/room.room')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/room.room')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/room.room')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/room.room')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
