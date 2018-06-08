<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Repositories\IpAddressRepository;
use Modules\Admin\Repositories\IpLoginFailRepository;
use Modules\Admin\Http\Requests\IpAddressCreateRequest;
use Modules\Admin\Http\Requests\IpAddressUpdateRequest;
use Modules\Admin\Http\Requests\LoginFailLogDeleteRequest;
use Modules\Admin\Models\IpAddress;
use Datatables;
use Illuminate\Support\Str;
use Auth;

class IpAddressController extends Controller
{

    /**
     * The IpAddressRepository instance.
     *
     * @var Modules\Admin\Repositories\IpAddressRepository
     */
    protected $repository;
    protected $loginFailRepository;

    /**
     * Create a new IpAddressController instance.
     *
     * @param  Modules\Admin\Repositories\IpAddressRepository $repository
     * @return void
     */
    public function __construct(IpAddressRepository $repository, IpLoginFailRepository $loginFailRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->loginFailRepository = $loginFailRepository;
    }

    //default method (verb/action - GET)
    public function index()
    {
        $data['page_title'] = 'Manage IP Addresses';
        return view('admin::ipaddress.index', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $ipAddresses = $this->repository->data();

        return Datatables::of($ipAddresses)
                ->addColumn('ids', function ($ipAddress) {
                    $checkbox = '';
                    if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($ipAddress->created_by == Auth::guard('admin')->user()->id))) {
                        $checkbox = '<input type="checkbox" name="ids[]" value="' . $ipAddress->id . '">';
                    }
                    return $checkbox;
                })
                ->addColumn('logindetails', function ($ipAddress) {
                    $ipLoginFailLog = (!empty($ipAddress->IpLoginFail)) ? $ipAddress->IpLoginFail : [];
                    $html = '';
                    if (!empty($ipLoginFailLog)) {
                        $html .= '<div class="ipaddress-login-details">';
                        $cnt = 1;
                        foreach ($ipLoginFailLog as $loginLog) {
                            $html .= '<div class="row">';
                            $html .= '<div class="col-md-9">' . $cnt . '. ' . $loginLog->username . ' <br/>' . $loginLog->access_time . '</div>';

                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($ipAddress->created_by == Auth::guard('admin')->user()->id))) {
                                $html .= '<div class="col-md-3"> <a href="javascript:;" data-id="' . $loginLog->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="Delete"><i class="fa fa-trash-o"></i></a></div></div>';
                            }

                            $cnt++;
                        }
                        $html .= '</div>';
                    }
                    return $html;
                })
                ->addColumn('status', function ($ipAddress) {
                    switch ($ipAddress->status) {
                        case 0:
                            $status = '<span class="label label-sm label-info">Pending</span>';
                            break;
                        case 1:
                            $status = '<span class="label label-sm label-success">Accepted</span>';
                            break;
                        case 2:
                            $status = '<span class="label label-sm label-danger">Rejected</span>';
                            break;
                    }
                    return $status;
                })
                ->addColumn('action', function ($ipAddress) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($ipAddress->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $ipAddress->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $ipAddress->id . '"><i class="fa fa-pencil"></i></a>';
                    }

                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {

                    //to display own records
                    if (Auth::guard('admin')->user()->hasOwnView) {
                        $instance->collection = $instance->collection->filter(function ($row) {
                            return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
                        });
                    }
                    if ($request->has('ip_address')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['ip_address'], $request->get('ip_address')) ? true : false;
                        });
                    }
                    if ($request->has('login_details')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            $rows = $row->toArray();
                            $rows['ip_login_fail'] = collect($rows['ip_login_fail'])->filter(function($row) use ($request) {
                                return Str::startsWith($row['username'], $request->get('login_details')) ? true : false;
                            });

                            return (!empty($rows['ip_login_fail']) && count($rows['ip_login_fail']) >= 1) ? true : false;
                        });
                    }
                    if ($request->has('status')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string) $row['status'], $request->get('status')) ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    /**
     * Hadle Ajax Group Action
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function groupAction(Request $request)
    {
        $response = $this->repository->groupAction($request->all());
        return response()->json($response);
    }

    //Add form (verb/action - GET)
    public function create()
    {
        return view('admin::ipaddress.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\IpAddressCreateRequest $request
     * @return json encoded Response
     */
    public function store(IpAddressCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified configuration category.
     *
     * @param  Modules\Admin\Models\ConfigCategory $configCategory
     * @return json encoded Response
     */
    public function edit(IpAddress $ipAddress)
    {
        $response['success'] = true;
        $response['form'] = view('admin::ipaddress.edit', compact('ipAddress'))->render();

        return response()->json($response);
    }

    public function update(IpAddressUpdateRequest $request, IpAddress $ipAddress)
    {
        $response = $this->repository->update($request->all(), $ipAddress);

        return response()->json($response);
    }

    /**
     * Delete record (verb/action - DELETE)
     *
     * @param  Modules\Admin\Http\Requests\LoginFailLogDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(LoginFailLogDeleteRequest $request)
    {
        $response = [];
        $result = $this->loginFailRepository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => 'IP Login details'])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => 'IP Login details'])];
        }

        return response()->json($response);
    }
}
