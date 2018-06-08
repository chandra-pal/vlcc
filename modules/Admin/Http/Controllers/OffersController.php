<?php

/**
 * The class for managing offers specific actions.
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\Offer;
use Modules\Admin\Repositories\OffersRepository;
use Modules\Admin\Http\Requests\OfferCreateRequest;
use Modules\Admin\Http\Requests\OfferUpdateRequest;
use Modules\Admin\Services\Helper\ImageHelper;
use Approached\LaravelImageOptimizer\ImageOptimizer;

class OffersController extends Controller {

    /**
     * The OffersRepository instance.
     *
     * @var Modules\Admin\Repositories\OffersRepository
     */
    protected $repository;

    /**
     * Create a new OffersController instance.
     *
     * @param  Modules\Admin\Repositories\OffersRepository $repository
     * @return void
     */
    public function __construct(OffersRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        return view('admin::offers.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $offers = $this->repository->data();

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $offers = Offer::filter(function ($row) {
                        return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
                    });
        }
        return Datatables::of($offers)
                        ->addColumn('action', function ($offers) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($offers->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $offers->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $offers->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })->addColumn('offer_image', function ($offers) {
                            return '<div class="user-listing-img">' . ImageHelper::getOfferImage($offers->id, $offers->offer_image) . '</div>';
                        })->addColumn('status', function ($offers) {
                            $status = ($offers->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new offer 
     *
     * @return view as response
     */
    public function create() {
        return view('admin::offers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OffersCreateRequest $request
     * @return json encoded Response
     */
    public function store(OfferCreateRequest $request) {
        $offerTitle = ucfirst($request->all()["offer_title"]);
        $updateOffer = $this->repository->updateOffers($offerTitle);
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified Offer.
     *
     * @param  Modules\Admin\Models\Offer $Offer
     * @return json encoded Response
     */
    public function edit(Offer $offers) {
        $response['success'] = true;
        $response['form'] = view('admin::offers.edit', compact('offers'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OffersCreateRequest $request, Modules\Admin\Models\Offer $offers
     * @return json encoded Response
     */
    public function update(OfferUpdateRequest $request, Offer $offers) {
        $response = $this->repository->update($request->all(), $offers);
        return response()->json($response);
    }

}
