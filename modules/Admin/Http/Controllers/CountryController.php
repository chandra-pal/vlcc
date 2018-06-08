<?php
/**
 * The class for managing country specific actions.
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
use Modules\Admin\Models\Country;
use Modules\Admin\Repositories\CountryRepository;
use Modules\Admin\Http\Requests\CountryCreateRequest;
use Modules\Admin\Http\Requests\CountryUpdateRequest;

class CountryController extends Controller
{

    /**
     * The CountryRepository instance.
     *
     * @var Modules\Admin\Repositories\CountryRepository
     */
    protected $repository;

    /**
     * Create a new CountryController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(CountryRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\CountryRepository $countryRepository
     * @return response
     */
    public function index()
    {
        return view('admin::country.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $countries = $this->repository->data($request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $countries = $countries->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($countries)
                ->addColumn('status_format', function ($country) {
                    $status = ($country->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($country) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($country->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $country->id . '" id="' . $country->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new country.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::country.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CountryCreateRequest $request
     * @return json encoded Response
     */
    public function store(CountryCreateRequest $request)
    {
        $response = $this->repository->store($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified country.
     *
     * @param  Modules\Admin\Models\Country $country
     * @return json encoded Response
     */
    public function edit(Country $country)
    {
        $response['success'] = true;
        $response['form'] = view('admin::country.edit', compact('country'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CountryUpdateRequest $request, Modules\Admin\Models\Country $country 
     * @return json encoded Response
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $response = $this->repository->update($request->all(), $country);

        return response()->json($response);
    }
}
