<?php

/**
 * The class for managing product specific actions.
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Modules\Admin\Models\Product;
use Modules\Admin\Repositories\ProductsRepository;
use Modules\Admin\Http\Requests\ProductCreateRequest;
use Modules\Admin\Http\Requests\ProductUpdateRequest;
use Modules\Admin\Services\Helper\ImageHelper;  

class ProductsController extends Controller {

    /**
     * The ProductsRepository instance.
     *
     * @var Modules\Admin\Repositories\ProductsRepository
     */
    protected $repository;

    /**
     * Create a new ProductsController instance.
     *
     * @param  Modules\Admin\Repositories\ProductsRepository $repository
     * @return void
     */
    public function __construct(ProductsRepository $repository) {
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
        return view('admin::products.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $products = $this->repository->data();

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $products = Product::filter(function ($row) {
                        return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
                    });
        } else {
            
        }
        return Datatables::of($products)
                        ->addColumn('action', function ($products) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($products->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $products->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $products->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })->addColumn('product_image', function ($products) {
                            return '<div class="user-listing-img">' . ImageHelper::getProductImage($products->id, $products->product_image) . '</div>';
                        })->addColumn('status', function ($products) {
                            $status = ($products->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new product 
     *
     * @return view as response
     */
    public function create() {
        return view('admin::products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductsCreateRequest $request
     * @return json encoded Response
     */
    public function store(ProductCreateRequest $request) {
        $productTitle = ucfirst($request->all()["product_title"]);
        $updateProduct = $this->repository->updateProducts($productTitle);
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param  Modules\Admin\Models\Product $Product
     * @return json encoded Response
     */
    public function edit(Product $products) {
        $response['success'] = true;
        $response['form'] = view('admin::products.edit', compact('products'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductsCreateRequest $request, Modules\Admin\Models\Product $products
     * @return json encoded Response
     */
    public function update(ProductUpdateRequest $request, Product $products) {
        $response = $this->repository->update($request->all(), $products);
        return response()->json($response);
    }

}
