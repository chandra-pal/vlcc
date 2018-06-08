<?php
/**
 * The class for managing user type specific actions.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Modules\Admin\Models\UserType;
use Modules\Admin\Repositories\UserTypeRepository;
use Modules\Admin\Http\Requests\UserTypeCreateRequest;
use Modules\Admin\Http\Requests\UserTypeUpdateRequest;

class UserTypeController extends Controller
{

    /**
     * The UserTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\UserTypeRepository
     */
    protected $repository;

    /**
     * Create a new UserTypeController instance.
     *
     * @param  Modules\Admin\Repositories\UserTypeRepository $repository
     * @return void
     */
    public function __construct(UserTypeRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     *
     * @param Modules\Admin\Repositories\UserTypeRepository $userTypeRepository
     * 
     * @return view
     */
    public function index()
    {
        $priorityList = $this->repository->listPriorityData()->toArray();

        return view('admin::user-type.index', compact('priorityList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $userTypes = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $userTypes = $userTypes->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($userTypes)
                ->filter(function ($instance) use ($request) {
                    if ($request->has('name')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['name']), strtolower($request->get('name'))) ? true : false;
                        });
                    }

                    if ($request->has('description')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['description']), strtolower($request->get('description'))) ? true : false;
                        });
                    }

                    if ($request->has('priority')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['priority']), strtolower($request->get('priority'))) ? true : false;
                        });
                    }

                    if ($request->has('status')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['status']), strtolower($request->get('status'))) ? true : false;
                        });
                    }
                    if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
                        $instance->collection = $instance->collection->filter(function ($row) {
                            return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
                        });
                    }
                })
                ->addColumn('status', function ($userType) {
                    $status = ($userType->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($userType) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($userType->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $userType->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $userType->id . '"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new user type.
     *
     * @return view as response
     */
    public function create()
    {
        $priorityList = $this->repository->listPriorityData()->toArray();

        return view('admin::user-type.create', compact('priorityList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\UserTypeCreateRequest $request
     * 
     * @return json encoded Response
     */
    public function store(UserTypeCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Display edit form
     *
     * @param Modules\Admin\Models\UserType $userType, Modules\Admin\Repositories\UserTypeRepository $userTypeRepository
     * 
     * @return json encoded response $response
     */
    public function edit(UserType $userType)
    {
        $priorityList = $this->repository->listPriorityData()->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::user-type.edit', compact('userType', 'priorityList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\UserTypeUpdateRequest $request, Modules\Admin\Models\UserType $userType 
     * @return json encoded Response
     */
    public function update(UserTypeUpdateRequest $request, UserType $userType)
    {
        $response = $this->repository->update($request->all(), $userType);

        return response()->json($response);
    }
}
