<?php namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Repositories\StateRepository;
use Modules\Admin\Repositories\CountryRepository;
use Modules\Admin\Http\Requests\StateCreateRequest;
use Modules\Admin\Http\Requests\StateUpdateRequest;
use Modules\Admin\Models\State;
use Datatables;
use Illuminate\Support\Str;
use Auth;

class StateController extends Controller
{

    /**
     * The StateRepository instance.
     *
     * @var Modules\Admin\Repositories\StateRepository
     */
    protected $repository;
    protected $countryRepository;

    /**
     * Create a new StateController instance.
     *
     * @param  Modules\Admin\Repositories\StateRepository $repository
     * @return void
     */
    public function __construct(StateRepository $repository, CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
    }

    //default method (verb/action - GET)
    public function index()
    {
        $data['page_title'] = 'Manage State';
        $countryList = $this->countryRepository->listCountryData()->toArray();
        return view('admin::state.index', compact('data', 'countryList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $data = $this->repository->data();
        //dd($data->toArray());
        return Datatables::of($data)
                ->addColumn('country_id', function ($result) {
                    return (!empty($result->Country->name)) ? $result->Country->name : '';
                })
                ->addColumn('status_format', function ($result) {
                    switch ($result->status) {
                        case 0:
                            $status = '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            break;
                        case 1:
                            $status = '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>';
                            break;
                    }
                    return $status;
                })
                ->addColumn('action', function ($result) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($result->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $result->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $result->id . '"><i class="fa fa-pencil"></i></a>';
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
                    if ($request->has('country_id')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string) $row['country_id'], $request->get('country_id')) ? true : false;
                        });
                    }
                    if ($request->has('name')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['name'], $request->get('name')) ? true : false;
                        });
                    }
                    if ($request->has('state_code')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['state_code'], strtoupper($request->get('state_code'))) ? true : false;
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
        $countryList = $this->countryRepository->listCountryData()->toArray();
        return view('admin::state.create',  compact('countryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\StateCreateRequest $request
     * @return json encoded Response
     */
    public function store(StateCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified configuration category.
     *
     * @param  Modules\Admin\Models\State $state
     * @return json encoded Response
     */
    public function edit(State $state)
    {
        //dd($state);
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $response['success'] = true;
        $response['form'] = view('admin::state.edit', compact('state', 'countryList'))->render();

        return response()->json($response);
    }

    public function update(StateUpdateRequest $request, State $state)
    {
        $response = $this->repository->update($request->all(), $state);

        return response()->json($response);
    }
}
