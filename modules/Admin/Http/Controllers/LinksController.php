<?php
/**
 * The class for Links manage specific actions.
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\LinksRepository,
    Modules\Admin\Repositories\LinkCategoryRepository,
    Modules\Admin\Repositories\UserTypeRepository,
    Modules\Admin\Http\Requests\LinksCreateRequest,
    Modules\Admin\Http\Requests\LinksUpdateRequest,
    Modules\Admin\Models\Links,
    Datatables,
    Illuminate\Support\Str,
    Response,
    Illuminate\Support\Facades\Auth,
    Illuminate\Support\Facades\Cache,
    Modules\Admin\Models\UserType,
    Illuminate\Http\Request;

class LinksController extends Controller
{

    /**
     * The LinksResponse instance.
     *
     * @var Modules\Admin\Repositories\LinksRepository
     */
    protected $repository;
    protected $categoryRepository;

    public function __construct(LinksRepository $repository, LinkCategoryRepository $categoryRepository, UserTypeRepository $usertype)
    {
        # User authentication filter
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->usertype = $usertype;
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
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/links.link')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/links.link')]);
        }
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getData(Request $request)
    {
        $links = $this->repository->data();
        //to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $links = $links->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($links)
                ->addColumn('ids', function ($link) {
                    if (!empty(\Auth::guard('admin')->user()->hasEdit)) {
                        return '<input type="checkbox" name="ids[]" value="' . $link->id . '">';
                    }
                })
                ->addColumn('linkcategoryicon', function ($link) {
                    $link_name = '<i class=' . $link->link_icon . '></i> &nbsp;' . $link->link_name;
                    return $link_name;
                })
                ->addColumn('status_format', function ($link) {
                    $status = ($link->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($link) {
                    $actionList = '';
                    if (!empty(\Auth::guard('admin')->user()->hasEdit) || (!empty(\Auth::guard('admin')->user()->hasOwnEdit) && ($link->created_by == \Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $link->id . '" class="btn btn-xs default yellow-gold margin-bottom-5 edit-form-link" id="' . $link->id . '"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('cat')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['cat']), strtolower($request->get('cat'))) ? true : false;
                        });
                    }
                    if ($request->has('link_name')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['link_name']), strtolower($request->get('link_name'))) ? true : false;
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $selectedUserTypes = [];
        $categoryNames = $this->categoryRepository->listCategoryData()->toArray();
        $paginationArray = $this->repository->getPaginationList();
        $userTypes = $this->usertype->listUserTypeData()->toArray();
        $linkList = [];
        return view('admin::links.index', compact('categoryNames', 'userTypes', 'selectedUserTypes', 'paginationArray', 'linkList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $selectedUserTypes = [];
        $categoryNames = $this->categoryRepository->listCategoryData()->toArray();
        $paginationArray = $this->repository->getPaginationList();
        $userTypes = $this->usertype->listUserTypeData()->toArray();
        return view('admin::links.create', compact('categoryNames', 'userTypes', 'selectedUserTypes', 'paginationArray'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Repositories\LinksRepository $links
     * @param  LinksCreateRequest $request
     * @return Response
     */
    public function store(LinksCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  Modules\Admin\Models\Links
     * @return Response
     */
    public function show(Links $links)
    {
        return view('admin::links.show', compact('links'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Modules\Admin\Models\Links
     * @return Response
     */
    public function edit(Links $links)
    {
        $categoryNames = $this->categoryRepository->listCategoryData()->toArray();
        $userTypes = $this->usertype->listUserTypeData()->toArray();
        $paginationArray = $this->repository->getPaginationList();
        $selectedUserTypes = $this->repository->getUserTypeIdsbyLink($links->id);
        Cache::tags(UserType::table())->flush();
        $response['success'] = true;
        $response['form'] = view('admin::links.edit', compact('links', 'categoryNames', 'userTypes', 'selectedUserTypes', 'paginationArray'))->render();

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\LinksUpdateRequest $request
     * @param  Modules\Admin\Models\Links
     * @return Response
     */
    public function update(LinksUpdateRequest $request, $links)
    {
        $response = $this->repository->update($request->all(), $links);
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getLinksData($categoryId)
    {
        $linkList = $this->repository->listLinksData($categoryId)->toArray();
        $response['list'] = View('admin::links.linkdropdown', compact('linkList'))->render();
        return response()->json($response);
    }
}
