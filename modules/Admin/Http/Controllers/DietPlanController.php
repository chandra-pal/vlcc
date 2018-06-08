<?php
/**
 * The class for managing diet plan specific actions.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\DietPlan;
use Modules\Admin\Repositories\DietPlanRepository;
use Modules\Admin\Http\Requests\DietPlanCreateRequest;
use Modules\Admin\Http\Requests\DietPlanUpdateRequest;
use Modules\Admin\Http\Requests\DietPlanDeleteRequest;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{

    /**
     * The DietPlanRepository instance.
     *
     * @var Modules\Admin\Repositories\DietPlanRepository
     */
    protected $repository;

    /**
     * Create a new DietPlanController instance.
     *
     * @param  Modules\Admin\Repositories\DietPlanRepository $repository
     * @return void
     */
    public function __construct(DietPlanRepository $repository)
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
        $dietType = array('1' => 'Veg', '2' => 'Non Veg');
        return view('admin::diet-plan.index', compact('dietType'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $dietPlan = $this->repository->data();
        $dietPlanTypeList = array("1" => "Veg", "2" => "Non Veg");
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $dietPlan = $dietPlan->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($dietPlan)
                ->addColumn('status', function ($dietPlan) {
                    $status = ($dietPlan->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('plan_type', function ($dietPlan) use($dietPlanTypeList) {
                    $plan_types = $dietPlanTypeList[$dietPlan->plan_type];
                    return $plan_types;
                })
                ->addColumn('action', function ($dietPlan) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($dietPlan->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $dietPlan->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $dietPlan->id . '"><i class="fa fa-pencil"></i></a>';
                    }
//                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($dietPlan->created_by == Auth::guard('admin')->user()->id))) {
//                                $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $dietPlan->id . ' created_by = ' . $dietPlan->created_by . ' ><i class="fa fa-trash-o"></i></a>';
//                            }
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('plan_name')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['plan_name']), strtolower($request->get('plan_name'))) ? true : false;
                        });
                    }
                                            
                    if ($request->has('plan_type')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['plan_type']), strtolower($request->get('plan_type'))) ? true : false;
                        });
                    }
                    if ($request->has('calories')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['calories']), strtolower($request->get('calories'))) ? true : false;
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
     * Display a form to create new diet plan category.
     *
     * @return view as response
     */
    public function create()
    {

        return view('admin::diet-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanCreateRequest $request
     * @return json encoded Response
     */
    public function store(DietPlanCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified diet plan.
     *
     * @param  Modules\Admin\Models\DietPlan $dietPlan
     * @return json encoded Response
     */
    public function edit(DietPlan $dietPlan)
    {
        $response['success'] = true;
        $dietType = array('1' => 'Veg', '2' => 'Non Veg');
        $response['form'] = view('admin::diet-plan.edit', compact('dietPlan', 'dietType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanRequest $request, Modules\Admin\Models\DietPlan $dietPlan
     * @return json encoded Response
     */
    public function update(DietPlanUpdateRequest $request, DietPlan $dietPlan)
    {
        $response = $this->repository->update($request->all(), $dietPlan);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(DietPlanDeleteRequest $request, DietPlan $dietPlan)
    {
        //dd("here" . $dietPlan);
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/diet-plan.diet-plan')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan.diet-plan')])];
        }

        return response()->json($response);
    }
}
