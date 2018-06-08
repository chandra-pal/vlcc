<?php
/**
 * The repository class for managing member diet deviation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Deviation;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;

class NotificationRepository extends BaseRepository
{

    /**
     * Create a new DeviationRepository instance.
     *
     * @param  Modules\Admin\Models\Deviation $model
     * @return void
     */
    public function __construct(Deviation $deviation)
    {
        $this->model = $deviation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);

        $request_details = DB::table('admin_notifications')
            ->where('admin_id', '=', $params['id'])
            ->orderBY('created_at','DESC')
            ->get();
        DB::setFetchMode(PDO::FETCH_CLASS);
        $response = collect($request_details);
        return $response;
    }

    public function updateNotificationStatus($params = [])
    {
        $result = DB::table('admin_notifications')
            ->where('id', $params["notification_id"])
            ->update(['read_status' => 1]);
        return $result;
    }
}
