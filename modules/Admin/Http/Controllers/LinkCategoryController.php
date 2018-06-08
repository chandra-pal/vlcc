<?php
/**
 * The class for linkcategory manage specific actions.
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\LinkCategoryRepository,
    Modules\Admin\Http\Requests\LinkCategoryCreateRequest,
    Modules\Admin\Http\Requests\LinkCategoryUpdateRequest,
    Modules\Admin\Models\LinkCategory,
    Datatables,
    Illuminate\Support\Facades\Auth,
    Response,
    Illuminate\Support\Str,
    Illuminate\Http\Request,
    Modules\Admin\Repositories\MenuGroupRepository;

class LinkCategoryController extends Controller
{

    /**
     * The LinkCategoryResponse instance.
     *
     * @var Modules\Admin\Repositories\LinkCategoryRepository
     */
    protected $repository;

    public function __construct(LinkCategoryRepository $repository, MenuGroupRepository $menuGroupRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->menuGroupRepository = $menuGroupRepository;
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
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/link-category.linkcategory')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/link-category.linkcategory')]);
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
        $LinkCategories = $this->repository->data();
        //to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $LinkCategories = $LinkCategories->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($LinkCategories)
                ->filter(function ($instance) use ($request) {
                    if ($request->has('category')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['category']), strtolower($request->get('category'))) ? true : false;
                        });
                    }
                    if ($request->has('description')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['header_text']), strtolower($request->get('description'))) ? true : false;
                        });
                    }

                    if ($request->has('status')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['status']), strtolower($request->get('status'))) ? true : false;
                        });
                    }

                    if ($request->has('menu_group_id')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['menu_group_id'], $request->get('menu_group_id')) ? true : false;
                        });
                    }
                })
                ->addColumn('ids', function ($LinkCategory) {
                    if (!empty(\Auth::guard('admin')->user()->hasEdit)) {
                        return '<input type="checkbox" name="ids[]" value="' . $LinkCategory->id . '">';
                    }
                })
                ->addColumn('categoryicon', function ($LinkCategory) {
                    $category = '<i class=' . $LinkCategory->category_icon . '></i> &nbsp;' . $LinkCategory->category;
                    return $category;
                })
                ->addColumn('status_format', function ($LinkCategory) {
                    $status = ($LinkCategory->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($LinkCategory) {
                    $actionList = '';
                    if (!empty(\Auth::guard('admin')->user()->hasEdit) || (!empty(\Auth::guard('admin')->user()->hasOwnEdit) && ($LinkCategory->created_by == \Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $LinkCategory->id . '" class="btn btn-xs default yellow-gold margin-bottom-5 edit-form-link" id="' . $LinkCategory->id . '"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
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
        $menuGroupNames = $this->menuGroupRepository->listMenuGroupData()->toArray();
        return view('admin::link-category.index', compact('menuGroupNames'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $menuGroupNames = $this->menuGroupRepository->listMenuGroupData()->toArray();
        return view('admin::link-category.create', compact('menuGroupNames'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Repositories\LinkCategoryRepository $linkCategory
     * @param  LinkCategoryCreateRequest $request
     * @return Response
     */
    public function store(
    LinkCategoryCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  Modules\Admin\Models\LinkCategory
     * @return Response
     */
    public function show(LinkCategory $linkCategory)
    {
        return view('admin::link-category.show', compact('linkCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Modules\Admin\Models\LinkCategory
     * @return Response
     */
    public function edit(LinkCategory $linkCategory)
    {
        $response['success'] = true;
        $menuGroupNames = $this->menuGroupRepository->listMenuGroupData()->toArray();
        $response['form'] = view('admin::link-category.edit', compact('linkCategory', 'menuGroupNames'))->render();

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\LinkCategoryUpdateRequest $request
     * @param  Modules\Admin\Models\LinkCategory
     * @return Response
     */
    public function update(
    LinkCategoryUpdateRequest $request, $linkCategory)
    {
        $response = $this->repository->update($request->all(), $linkCategory);
        $response['sidebar'] = view('admin::layouts.sidebar')->render();
        return response()->json($response);
    }
}
