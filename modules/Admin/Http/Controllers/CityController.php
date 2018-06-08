<?php
/**
 * The class for managing city specific actions.
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
use Illuminate\Http\Request;
use Modules\Admin\Models\City;
use Modules\Admin\Repositories\CityRepository;
use Modules\Admin\Repositories\CountryRepository;
use Modules\Admin\Repositories\StateRepository;
use Modules\Admin\Http\Requests\CityCreateRequest;
use Modules\Admin\Http\Requests\CityUpdateRequest;

class CityController extends Controller
{

    /**
     * The CityRepository instance.
     *
     * @var Modules\Admin\Repositories\CityRepository
     */
    protected $repository;

    /**
     * The CountryRepository instance.
     *
     * @var Modules\Admin\Repositories\CountryRepository
     */
    protected $countryRepository;

    /**
     * The StateRepository instance.
     *
     * @var Modules\Admin\Repositories\StateRepository
     */
    protected $stateRepository;

    /**
     * Create a new CityController instance.
     *
     * @param  Modules\Admin\Repositories\CityRepository $repository,
     *  Modules\Admin\Repositories\CountryRepository $countryRepository,
     *  Modules\Admin\Repositories\StateRepository $stateRepository
     * 
     * @return void
     */
    public function __construct(CityRepository $repository, CountryRepository $countryRepository, StateRepository $stateRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
        $this->stateReposotiry = $stateRepository;
    }

    /**
     * List all the data
     * 
     * @return view
     */
    public function index()
    {
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = [];

        return view('admin::city.index', compact('countryList', 'stateList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $cities = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $cities = $cities->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($cities)
                ->addColumn('status', function ($city) {
                    $status = ($city->status) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($city) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($city->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $city->id . '" id="' . $city->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
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
                            return Str::equals((string)$row['country_id'], $request->get('country_id')) ? true : false;
                        });
                    }
                    if ($request->has('state_id')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string)$row['state_id'], strtoupper($request->get('state_id'))) ? true : false;
                        });
                    }
                    if ($request->has('name')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains(strtolower($row['name']), strtolower($request->get('name'))) ? true : false;
                        });
                    }
                    if ($request->has('status')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string)$row['status'], $request->get('status')) ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getStateData($countryId)
    {
        $stateList = $this->stateReposotiry->listStateData($countryId)->toArray();
        $response['list'] = View('admin::city.statedropdown', compact('stateList'))->render();
        return response()->json($response);
    }

    /**
     * Display a form to create new city.
     *
     * @return view as response
     */
    public function create()
    {
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = [];

        return view('admin::city.create', compact('countryList', 'stateList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CityCreateRequest $request
     * @return json encoded Response
     */
    public function store(CityCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified City.
     *
     * @param  Modules\Admin\Models\City $city
     * @return json encoded Response
     */
    public function edit(City $city)
    {
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = $this->stateReposotiry->listStateData($city->country_id)->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::city.edit', compact('city', 'countryList', 'stateList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CityUpdateRequest $request, Modules\Admin\Models\City $city
     * @return json encoded Response
     */
    public function update(CityUpdateRequest $request, City $city)
    {
        $response = $this->repository->update($request->all(), $city);

        return response()->json($response);
    }
}
