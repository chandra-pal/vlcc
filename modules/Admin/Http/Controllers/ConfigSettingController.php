<?php
/**
 * The class for managing configuration settings specific actions.
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
use Illuminate\Support\Str;
use Modules\Admin\Models\ConfigSetting;
use Modules\Admin\Repositories\ConfigCategoryRepository;
use Modules\Admin\Repositories\ConfigSettingRepository;
use Modules\Admin\Http\Requests\ConfigSettingCreateRequest;
use Modules\Admin\Http\Requests\ConfigSettingUpdateRequest;

class ConfigSettingController extends Controller
{

    /**
     * The ConfigSettingRepository instance.
     *
     * @var Modules\Admin\Repositories\ConfigSettingRepository
     */
    protected $repository;

    /**
     * The ConfigCategoryRepository instance.
     *
     * @var Modules\Admin\Repositories\ConfigCategoryRepository
     */
    protected $configCategoryRepository;

    /**
     * Create a new ConfigSettingController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository,
     *  Modules\Admin\Repositories\ConfigCategoryRepository $configCategoryRepository
     * @return void
     */
    public function __construct(ConfigSettingRepository $repository, ConfigCategoryRepository $configCategoryRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->configCategoryRepository = $configCategoryRepository;
    }

    /**
     * List all the data
     * 
     * @return response
     */
    public function index()
    {
        $categoryList = $this->configCategoryRepository->listCategoryData()->toArray();

        return view('admin::config-setting.index', compact('categoryList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $configSettings = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $configSettings = $configSettings->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($configSettings)
                ->addColumn('action', function ($configSetting) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($configSetting->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $configSetting->id . '" id="' . $configSetting->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new configuration setting.
     *
     * @return view as response
     */
    public function create()
    {
        $categoryList = $this->configCategoryRepository->listCategoryData()->toArray();
        return view('admin::config-setting.create', compact('categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ConfigSettingCreateRequest $request
     * @return json encoded Response
     */
    public function store(ConfigSettingCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified configuration setting.
     *
     * @param  Modules\Admin\Models\ConfigSetting $configSetting, Modules\Admin\Repositories\ConfigCategoryRepository $configCategoryRepository
     * @return json encoded Response
     */
    public function edit(ConfigSetting $configSetting)
    {
        $categoryList = $this->configCategoryRepository->listCategoryData()->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::config-setting.edit', compact('configSetting', 'categoryList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ConfigSettingUpdateRequest $request, Modules\Admin\Models\ConfigSetting $configSetting 
     * @return json encoded Response
     */
    public function update(ConfigSettingUpdateRequest $request, ConfigSetting $configSetting)
    {
        $response = $this->repository->update($request->all(), $configSetting);

        return response()->json($response);
    }
}
