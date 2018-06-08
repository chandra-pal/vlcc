<?php
/**
 * The class for managing menu groups specific actions.
 *
 *
 * @author Prashant Birajdar <prashantb@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\MenuGroup;
use Modules\Admin\Repositories\MenuGroupRepository;
use Modules\Admin\Http\Requests\MenuGroupCreateRequest;
use Modules\Admin\Http\Requests\MenuGroupUpdateRequest;

class MenuGroupController extends Controller
{

    /**
     * The MenuGroupRepository instance.
     *
     * @var Modules\Admin\Repositories\MenuGroupRepository
     */
    protected $repository;

    /**
     * Create a new MenuGroupController instance.
     *
     * @param  Modules\Admin\Repositories\MenuGroupRepository $repository
     * @return void
     */
    public function __construct(MenuGroupRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index()
    {
        return view('admin::menu-group.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData()
    {
        $menuGroups = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $menuGroups = $menuGroups->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($menuGroups)
                ->addColumn('status', function ($menuGroups) {
                    $status = ($menuGroups->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($menuGroups) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($menuGroups->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $menuGroups->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $menuGroups->id . '"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new menu group.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::menu-group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MenuGroupCreateRequest $request
     * @return json encoded Response
     */
    public function store(MenuGroupCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified menu group.
     *
     * @param  Modules\Admin\Models\MenuGroup $menuGroup
     * @return json encoded Response
     */
    public function edit(MenuGroup $menuGroup)
    {

        $response['success'] = true;
        $response['form'] = view('admin::menu-group.edit', compact('menuGroup'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MenuGroupCreateRequest $request, Modules\Admin\Models\MenuGroup $menuGroup
     * @return json encoded Response
     */
    public function update(MenuGroupUpdateRequest $request, MenuGroup $menuGroup)
    {
        $response = $this->repository->update($request->all(), $menuGroup);
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }
}
