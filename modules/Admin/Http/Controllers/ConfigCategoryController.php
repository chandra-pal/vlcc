<?php
/**
 * The class for managing categories specific actions.
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
use Modules\Admin\Models\ConfigCategory;
use Modules\Admin\Repositories\ConfigCategoryRepository;
use Modules\Admin\Http\Requests\ConfigCategoryCreateRequest;
use Modules\Admin\Http\Requests\ConfigCategoryUpdateRequest;

class ConfigCategoryController extends Controller
{

    /**
     * The ConfigCategoryRepository instance.
     *
     * @var Modules\Admin\Repositories\ConfigCategoryRepository
     */
    protected $repository;

    /**
     * Create a new ConfigCategoriesController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigCategoryRepository $repository
     * @return void
     */
    public function __construct(ConfigCategoryRepository $repository)
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
        return view('admin::config-category.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData()
    {
        $configCategories = $this->repository->data();
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $configCategories = $configCategories->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($configCategories)
                ->addColumn('action', function ($configCategory) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($configCategory->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $configCategory->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $configCategory->id . '"><i class="fa fa-pencil"></i></a>';
                    }

                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new configuration category.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::config-category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ConfigCategoryCreateRequest $request
     * @return json encoded Response
     */
    public function store(ConfigCategoryCreateRequest $request)
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
    public function edit(ConfigCategory $configCategory)
    {
        $response['success'] = true;
        $response['form'] = view('admin::config-category.edit', compact('configCategory'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ConfigCategoryCreateRequest $request, Modules\Admin\Models\ConfigCategory $configCategory
     * @return json encoded Response
     */
    public function update(ConfigCategoryUpdateRequest $request, ConfigCategory $configCategory)
    {
        $response = $this->repository->update($request->all(), $configCategory);

        return response()->json($response);
    }
}
