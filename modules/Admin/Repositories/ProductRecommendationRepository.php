<?php

/**
 * The repository class for managing ProductRecommendation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ProductRecommendation;
use Modules\Admin\Models\Product;
use Exception;
use Route;
use Log;
use Cache;
use Auth;
use Session;
use Illuminate\Http\Request;

class ProductRecommendationRepository extends BaseRepository {

    /**
     * Create a new ProductRecommendationRepository instance.
     *
     * @param  Modules\Admin\Models\ProductRecommendation $model
     * @return void
     */
    public function __construct(ProductRecommendation $productRecommendation) {
        $this->model = $productRecommendation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));

        $member_id = Session::get('member_id');
        $created_by = Auth::guard('admin')->user()->id;
        $user_type_id = Auth::guard('admin')->user()->user_type_id;

        if (isset($user_type_id) && ($user_type_id == '4' || $user_type_id == '8')) {
            if (isset($member_id) && $member_id != '') {
                //Cache::tags not suppport with files and Database
                $response = Cache::tags(ProductRecommendation::table())->remember($cacheKey, $this->ttlCache, function() {

                    return ProductRecommendation::with('product')->where([['member_id', '=', Session::get('member_id')], ['created_by', '=', Auth::guard('admin')->user()->id]])
                                    ->orderBy('id', 'DESC')
                                    ->get();
                });
            } else {
                return ProductRecommendation::with('product')->where([['member_id', '=', ''], ['created_by', '=', Auth::guard('admin')->user()->id]])
                                ->orderBy('id', 'DESC')
                                ->get();
            }
        } else {
            //Cache::tags not suppport with files and Database
            $response = Cache::tags(ProductRecommendation::table())->remember($cacheKey, $this->ttlCache, function() {
                return ProductRecommendation::with('product')->where([['created_by', '=', Auth::guard('admin')->user()->id]])
                                ->orderBy('id', 'DESC')
                                ->get();
            });
        }
        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getProductList() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Product::table())->remember($cacheKey, $this->ttlCache, function() {
            return Product::whereStatus(1)->orderBy('id')->lists('product_title', 'id');
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs) {
        $user_type_id = Auth::guard('admin')->user()->user_type_id;
        try {
            foreach ($inputs['product_id'] as $product_Key => $product_value) {
                $productRecommendation = new $this->model;
                $productRecommendation->product_id = $inputs['product_id'][$product_Key];
                if (isset($inputs['member_id']) && $inputs['member_id'] != '0') {
                    $productRecommendation->member_id = $inputs['member_id'];
                } elseif ($user_type_id == '1') {
                    $productRecommendation->member_id = '0';
                }
                $productRecommendation->created_by = Auth::guard('admin')->user()->id;
                $save[] = $productRecommendation->save();
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an product recommendation.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ProductRecommendation $productRecommendation
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $productRecommendation) {
        try {

            foreach ($inputs['product_id'] as $product_Key => $product_value) {
                $productRecommendation->product_id = $inputs['product_id'][$product_Key];
                $productRecommendation->status = $inputs['status'];
                $productRecommendation->updated_by = Auth::guard('admin')->user()->id;
                $save[] = $productRecommendation->save();
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/product-recommendation.product-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
