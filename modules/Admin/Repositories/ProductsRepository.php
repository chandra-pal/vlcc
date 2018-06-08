<?php

/**
 * The repository class for managing product specific actions.
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Product;
use Modules\Admin\Services\Helper\ImageHelper;
use Approached\LaravelImageOptimizer\ImageOptimizer;
use Exception;
use Route;
use Log;
use Cache;
use File;

class ProductsRepository extends BaseRepository {

    /**
     * Create a new ProductsRepository instance.
     *
     * @param  Modules\Admin\Models\Product $products
     * @return void
     */
    public function __construct(Product $products) {
        $this->model = $products;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Product::table())->remember($cacheKey, $this->ttlCache, function() {
            return Product::orderBy('id', 'DESC')
                            ->get();
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, $product_id = null) {
        try {
            $products = new $this->model;

            $allColumns = $products->getTableColumns($products->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $products->$key = $value;
                }
            }
            $products->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $products->product_title = ucfirst($inputs['product_title']);
            $save = $products->save();
            $this->updateProductImage($inputs, $products);
            if ($save) {
                $response['status'] = 'success';
                $response['products_id'] = $products->id;
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/products.products')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/products.products')]);
                $response['products_id'] = '';
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/products.products')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/products.products')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an products.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Product $products
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $products) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($products->$key)) {
                    $products->$key = $value;
                }
            }
            $products->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $products->product_title = ucfirst($inputs['product_title']);
            $save = $products->save();

            $this->updateProductImage($inputs, $products);
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/products.products')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/products.products')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/products.products')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/products.products')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to Update product status as Inactive if same products name exists & created by Customer
    public function updateProducts($productTitle) {
        return Product::where('product_title', "=", $productTitle);
    }

    /**
     * Update Product image.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Product $products
     * @return void
     */
    public function updateProductImage($inputs, $products) {
        if (!empty($inputs['thefile'])) {
            //unlink old file
            if (!empty($products->product_image)) {
                File::Delete(public_path() . ImageHelper::getProductUploadFolder($products->id) . $products->product_image);
            }
            $imageOptimizer = new ImageOptimizer;
            $products->product_image = ImageHelper::uploadProductImage($inputs['thefile'], $products, $imageOptimizer);
            $products->save();
        } else if (isset($inputs['remove']) && $inputs['remove'] == 'remove') {
            $products->product_image = '';
            $products->save();
        } else {
            $products->save();
        }
    }

}
