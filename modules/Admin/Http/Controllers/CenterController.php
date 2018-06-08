<?php

/**
 * The class for managing center specific actions.
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
use Modules\Admin\Models\Center;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Repositories\CountryRepository;
use Modules\Admin\Repositories\StateRepository;
use Modules\Admin\Repositories\CityRepository;
use Modules\Admin\Http\Requests\CenterCreateRequest;
use Modules\Admin\Http\Requests\CenterUpdateRequest;
use Modules\Admin\Http\Requests\CenterDeleteRequest;
use Illuminate\Http\Request;
use Session;

class CenterController extends Controller {

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $repository;
    protected $countryRepository;
    protected $stateRepository;
    protected $cityRepository;

    /**
     * Create a new CenterController instance.
     *
     * @param  Modules\Admin\Repositories\CenterRepository $repository
     * @return void
     */
    public function __construct(CenterRepository $repository, CountryRepository $countryRepository, StateRepository $stateRepository, CityRepository $cityRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
        $this->stateReposotiry = $stateRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = [];
        $cityList = [];
        return view('admin::center.index', compact('countryList', 'stateList', 'cityList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $centers = $this->repository->data();

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $centers = $centers->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($centers)
                        /* ->addColumn('country',function($centers){
                          $str = '-';
                          if(!empty($centers->country) || !empty($centers->states) || !empty($centers->city)){
                          $str = $centers->city->name.', ';
                          $str .= $centers->states->name.' (';
                          $str .= $centers->country->name.')';
                          }

                          return $str;
                          }) */
                        ->addColumn('action', function ($centers) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($centers->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $centers->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $centers->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->addColumn('status', function ($centers) {
                            $status = ($centers->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->filter(function ($instance) use ($request) {

                            //to display own records
                            if ($request->has('center_name')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains(strtolower($row['center_name']), strtolower($request->get('center_name'))) ? true : false;
                                });
                            }

                            if ($request->has('address')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains(strtolower($row['address']), strtolower($request->get('address'))) ? true : false;
                                });
                            }
                            if ($request->has('area')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains(strtolower($row['area']), strtolower($request->get('area'))) ? true : false;
                                });
                            }

                            if ($request->has('country_id')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return (Str::contains($row['country_id'], $request->get('country_id')) || Str::contains($row['state_id'], $request->get('country_id')) || Str::contains($row['city_id'], $request->get('country_id'))) ? true : false;
                                });
                            }
                            /* if ($request->has('state_id')) {

                              $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                              return Str::equals((string) $row['state_id'], $request->get('state_id')) ? true : false;
                              });
                              } */
                            /* if ($request->has('city_id')) {

                              $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                              return Str::equals((string) $row['city_id'], $request->get('city_id')) ? true : false;
                              });
                              } */


                            if ($request->has('phone_number')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains(strtolower($row['phone_number']), strtolower($request->get('phone_number'))) ? true : false;
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
    public function getStateData($countryId) {
        $stateList = $this->stateReposotiry->listStateData($countryId)->toArray();
        $response['list'] = View('admin::center.statedropdown', compact('stateList'))->render();
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getCityData($stateId) {
        $cityList = $this->cityRepository->listCityData($stateId)->toArray();
        $response['list'] = View('admin::center.citydropdown', compact('cityList'))->render();
        return response()->json($response);
    }

    /**
     * Display a form to create new center
     *
     * @return view as response
     */
    public function create() {
        return view('admin::center.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CenterCreateRequest $request
     * @return json encoded Response
     */
    public function store(CenterCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified center.
     *
     * @param  Modules\Admin\Models\Center $center
     * @return json encoded Response
     */
    public function edit(Center $center) {
        $response['success'] = true;
        $data = $center->toArray();
        $countryId = $data['country_id'];
        $stateId = $data['state_id'];
        $countryList = $this->countryRepository->listCountryData()->toArray();
        $stateList = $this->stateReposotiry->listStateData($countryId)->toArray();
        $cityList = $this->cityRepository->listCityData($stateId)->toArray();

        $response['form'] = view('admin::center.edit', compact('center', 'countryList', 'stateList', 'cityList'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CenterCreateRequest $request, Modules\Admin\Models\Center $center
     * @return json encoded Response
     */
    public function update(CenterUpdateRequest $request, Center $center) {
        $response = $this->repository->update($request->all(), $center);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\CenterDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(CenterDeleteRequest $request, Center $center) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/center.center')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/center.center')])];
        }

        return response()->json($response);
    }
}
