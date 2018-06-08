<?php
/**
 * The class for managing admin user's notification specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Repositories\NotificationRepository;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    /**
     * The DeviationRepository instance.
     *
     * @var Modules\Admin\Repositories\NotificationRepository
     */
    protected $repository;

    /**
     * Create a new DeviationController instance.
     *
     * @param  Modules\Admin\Repositories\NotificationRepository $repository
     * @return void
     */
    public function __construct(NotificationRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $params['id'] = Auth::guard('admin')->user()->id;

        $response = $this->repository->data($params)->toArray();

        return response()->json($response);
    }

    public function readNotifications(Request $request) {
        $params["notification_id"] = $request->all()["notification_id"];
        $result = $this->repository->updateNotificationStatus($params);
        if ($result) {
            $response["status"] = "success";
        } else {
            $response["status"] = "error";
        }
        return $response;
    }
}
