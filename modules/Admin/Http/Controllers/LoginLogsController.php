<?php
/**
 * The class for Links manage specific actions.
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\LoginLogsRepository,
    Modules\Admin\Repositories\UserRepository,
    Illuminate\Http\Request,
    Illuminate\Support\Str,
    Illuminate\Support\Facades\Auth,
    Datatables,
    Modules\Admin\Services\Helper\ConfigConstantHelper;

class LoginLogsController extends Controller
{

    /**
     * The RolesRepository instance.
     *
     * @var Modules\Admin\Repositories\RolesRepository
     */
    protected $repository;

    /**
     * Create a new RolesController instance.
     *
     * @param  Modules\Admin\Repositories\AdminRolesRepository $roles_gestion
     * @return void
     */
    public function __construct(LoginLogsRepository $repository, UserRepository $userRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * List all the data
     *
     * @return type
     */
    public function index()
    {
        $groupActions = $this->repository->getGroupActionData();
        return view('admin::login-logs.index', compact('results', 'action', 'groupActions'));
    }

    /**
     * Hadle Ajax Group Action
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function groupAction(Request $request)
    {
        $response = [];
        $result = $this->repository->groupAction($request->all());
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.deleted', ['name' => 'Record']);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.notDeleted', ['name' => 'Record']);
        }

        return response()->json($response);
    }

    public function getData(Request $request)
    {
        $loginLogs = $this->repository->data();

        return Datatables::of($loginLogs)
                ->addColumn('ids', function ($loginLogs) {
                    $checkbox = '';
                    if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($loginLogs->user_id == Auth::guard('admin')->user()->id))) {
                        $checkbox = '<input type="checkbox" name="ids[]" value="' . $loginLogs->id . '">';
                    }
                    return $checkbox;
                })
                ->addColumn('in_time', function ($loginLogs) {
                    $dateTimeFormat = ConfigConstantHelper::getValue('C_DATEFORMAT');
                    return date($dateTimeFormat, strtotime($loginLogs->in_time));
                })
                ->addColumn('last_access_time', function ($loginLogs) {
                    return date('h:i A', strtotime($loginLogs->last_access_time));
                })
                ->addColumn('logout_time', function ($loginLogs) {
                    $dateTimeFormat = ConfigConstantHelper::getValue('C_DATEFORMAT');
                    return ($loginLogs->out_time === '0000-00-00 00:00:00') ? '0000-00-00 00:00:00' : date($dateTimeFormat, strtotime($loginLogs->out_time));
                })
                ->addColumn('action', function ($loginLogs) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($loginLogs->user_id == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $loginLogs->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                    }
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {
                    //to display own records
                    if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
                        $instance->collection = $instance->collection->filter(function ($row) {
                            return (Str::equals($row['user_id'], Auth::guard('admin')->user()->id)) ? true : false;
                        });
                    }
                    if ($request->has('user_name')) {
                        $passUserName = $request->get('user_name');
                        $instance->collection = $instance->collection->filter(function ($row) use ($passUserName) {
                            return Str::contains($row['user']['username'], $passUserName) ? true : false;
                        });
                    }
                    if ($request->has('ip_address')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains($row['ip_address'], $request->get('ip_address')) ? true : false;
                        });
                    }
                    if ($request->has('login_in_time_from') && $request->has('login_in_time_to')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            $fromDateArray = explode("-", $request->get('login_in_time_from'));
                            $toDateArray = explode("-", $request->get('login_in_time_to'));
                            $fromDate = date('Y-m-d', strtotime($fromDateArray[0])) . ' ' . '00:00:00';
                            $toDate = date('Y-m-d', strtotime($toDateArray[0])) . ' ' . '23:59:59';
                            return $row['in_time'] >= $fromDate && $row['in_time'] <= $toDate ? true : false;
                        });
                    }
                    if ($request->has('logout_out_time_from') && $request->has('logout_out_time_to')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            $fromDateArray = explode("-", $request->get('logout_out_time_from'));
                            $toDateArray = explode("-", $request->get('logout_out_time_to'));
                            $fromDate = date('Y-m-d', strtotime($fromDateArray[0])) . ' ' . '00:00:00';
                            $toDate = date('Y-m-d', strtotime($toDateArray[0])) . ' ' . '23:59:59';
                            return $row['out_time'] >= $fromDate && $row['out_time'] <= $toDate ? true : false;
                        });
                    }
                    if ($request->has('access_time_from') && $request->has('access_time_to')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            $accessTime = strtotime(date('h:i A', strtotime($row['last_access_time'])));
                            return $accessTime >= strtotime($request->get('access_time_from')) && $accessTime <= strtotime($request->get('access_time_to')) ? true : false;
                        });
                    }
                })
                ->make(true);
    }
}
