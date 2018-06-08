<?php
/**
 * The class for managing locations specific actions.
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Models\Locations;
use Modules\Admin\Repositories\LocationsRepository;
use Modules\Admin\Repositories\CityRepository;
use Modules\Admin\Repositories\CountryRepository;
use Modules\Admin\Repositories\StateRepository;
use Modules\Admin\Http\Requests\LocationsCreateRequest;
use Modules\Admin\Http\Requests\LocationsUpdateRequest;

class LocationsController extends Controller
{

    /**
     * The LocationsRepository instance.
     *
     * @var Modules\Admin\Repositories\LocationsRepository
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
     * The CityRepository instance.
     *
     * @var Modules\Admin\Repositories\CityRepository
     */
    protected $cityRepository;

    /**
     * Create a new LocationsController instance.
     *
     * @param  Modules\Admin\Repositories\LocationsRepository $repository,
     *  Modules\Admin\Repositories\CityRepository $cityRepository,
     *  Modules\Admin\Repositories\StateRepository $stateRepository
     *  Modules\Admin\Repositories\CountryRepository $countryRepository,
     *
     * @return void
     */
    public function __construct(LocationsRepository $repository, CityRepository $cityRepository, CountryRepository $countryRepository, StateRepository $stateRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->cityRepository = $cityRepository;
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
        $cityList = [];

        return view('admin::locations.index', compact('countryList', 'stateList', 'cityList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $locations = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $locations = $locations->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($locations)
                ->addColumn('action', function ($location) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($location->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $location->id . '" id="' . $location->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
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
                    if ($request->has('state_id')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string) $row['state_id'], $request->get('state_id')) ? true : false;
                        });
                    }
                    if ($request->has('city_id')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::equals((string) row['city_id'], $request->get('city_id')) ? true : false;
                        });
                    }
                    if ($request->has('location')) {

                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains(strtolower($row['location']), strtolower($request->get('location'))) ? true : false;
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
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getStateData($countryId)
    {
        //echo '$countryId=>'.$countryId;die;
        $stateList = $this->stateReposotiry->listStateData($countryId)->toArray();
        $response['list'] = View('admin::locations.statedropdown', compact('stateList'))->render();
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getCityData($stateId)
    {
        $cityList = $this->cityRepository->listCityData($stateId)->toArray();
        $response['list'] = View('admin::locations.citydropdown', compact('cityList'))->render();
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
        $cityList = [];

        return view('admin::locations.create', compact('countryList', 'stateList', 'cityList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\LocationsCreateRequest $request
     * @return json encoded Response
     */
    public function store(LocationsCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified Locations.
     *
     * @param  Modules\Admin\Models\Locations $locations
     * @return json encoded Response
     */
    public function edit(Locations $locations)
    {
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = $this->stateReposotiry->listStateData($locations->country_id)->toArray();
        $cityList = $this->cityRepository->listCityData($locations->state_id)->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::locations.edit', compact('locations', 'countryList', 'stateList', 'cityList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CityUpdateRequest $request, Modules\Admin\Models\City $city
     * @return json encoded Response
     */
    public function update(LocationsUpdateRequest $request, Locations $locations)
    {
        $response = $this->repository->update($request->all(), $locations);

        return response()->json($response);
    }
}
