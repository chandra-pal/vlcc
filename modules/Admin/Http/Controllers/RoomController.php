<?php

/**
 * The class for managing room specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Models\Room;
use Modules\Admin\Repositories\RoomRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\RoomCreateRequest;
use Modules\Admin\Http\Requests\RoomUpdateRequest;

class RoomController extends Controller {

    /**
     * The RoomRepository instance.
     *
     * @var Modules\Admin\Repositories\RoomRepository
     */
    protected $repository;
    protected $centerRepository;

    /**
     * Create a new RoomController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(RoomRepository $repository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\RoomRepository $roomRepository
     * @return response
     */
    public function index() {
        // $centerList = $this->centerRepository->listCenterData()->toArray();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $searchCenterList = [];
        if (!empty($centerList)) {
            foreach ($centerList as $key => $value) {
                $searchCenterList[$value] = $value;
            }
        }
        return view('admin::room.index', compact('data', 'centerList', 'searchCenterList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $rooms = $this->repository->data($params);
        $room = $rooms->toArray();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $rooms = $rooms->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($rooms)
                        ->addColumn('cname', function ($room) {
                            return (!empty($room['cname']) ? $room['cname'] : '');
                        })
                        ->addColumn('name', function ($room) {
                            return (!empty($room['name']) ? $room['name'] : '');
                        })
                        ->addColumn('room_type', function ($room) {
                            if ($room['room_type'] == 1) {
                                $room_type = "Male";
                            } else if ($room['room_type'] == 2) {
                                $room_type = "Female";
                            } else {
                                $room_type = "Common";
                            }
                            return $room_type;
                        })
                        ->addColumn('status_format', function ($room) {
                            switch ($room['status']) {
                                case 0:
                                    $status = '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                                    break;
                                case 1:
                                    $status = '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>';
                                    break;
                            }
                            return $status;
                        })
                        ->addColumn('action', function ($room) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($room->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $room['id'] . '" id="' . $room['id'] . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
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
                            if ($request->has('cname')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['cname']), strtolower($request->get('cname'))) ? true : false;
                                });
                            }
                            if ($request->has('name')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['name']), strtolower($request->get('name'))) ? true : false;
                                });
                            }

                            if ($request->has('room_type')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['room_type']), strtolower($request->get('room_type'))) ? true : false;
                                });
                            }

                            if ($request->has('status')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['status']), strtolower($request->get('status'))) ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

    /**
     * Handle Ajax Group Action
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function groupAction(Request $request) {
        $response = [];
        $result = $this->repository->groupAction($request->all());
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/room.room')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/room.room')]);
        }
        return response()->json($response);
    }

    /**
     * Display a form to create new room.
     *
     * @return view as response
     */
    public function create() {
        //$centerList = $this->centerRepository->listCenterData()->toArray();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        return view('admin::room.create', compact('centerList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RoomCreateRequest $request
     * @return json encoded Response
     */
    public function store(RoomCreateRequest $request) {
        $response = $this->repository->store($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified room.
     *
     * @param  Modules\Admin\Models\Room $room
     * @return json encoded Response
     */
    public function edit(Room $room) {
        // $centerList = $this->centerRepository->listCenterData()->toArray();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $response['success'] = true;
        $response['form'] = view('admin::room.edit', compact('room', 'centerList'))->render();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RoomUpdateRequest $request, Modules\Admin\Models\Room $room
     * @return json encoded Response
     */
    public function update(RoomUpdateRequest $request, Room $room) {
        $response = $this->repository->update($request->all(), $room);
        return response()->json($response);
    }

}
