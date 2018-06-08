<?php

/**
 * The repository class for managing offers specific actions.
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Offer;
use Modules\Admin\Services\Helper\ImageHelper;
use Approached\LaravelImageOptimizer\ImageOptimizer;
use Exception;
use Route;
use Auth;
use Log;
use Cache;
use URL;
use File;
use DB;
use PDO;

class OffersRepository extends BaseRepository {

    /**
     * Create a new OffersRepository instance.
     *
     * @param  Modules\Admin\Models\Offer $model
     * @return void
     */
    public function __construct(Offer $offers) {
        $this->model = $offers;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Offer::table())->remember($cacheKey, $this->ttlCache, function() {
            return Offer::orderBy('id', 'DESC')
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
    public function create($inputs, $offer_id = null) {
        try {
            $offers = new $this->model;

            $allColumns = $offers->getTableColumns($offers->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $offers->$key = $value;
                }
            }
            $offers->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $offers->offer_title = ucfirst($inputs['offer_title']);
            $save = $offers->save();
            $this->updateOfferImage($inputs, $offers);
            if ($save) {
                $response['status'] = 'success';
                $response['offers_id'] = $offers->id;
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/offers.offers')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/offers.offers')]);
                $response['offers_id'] = '';
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/offers.offers')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/offers.offers')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an offers.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Offer $offers
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $offers) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($offers->$key)) {
                    $offers->$key = $value;
                }
            }
            $offers->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $offers->offer_title = ucfirst($inputs['offer_title']);
            $save = $offers->save();

            $this->updateOfferImage($inputs, $offers);
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/offers.offers')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/offers.offers')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/offers.offers')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/offers.offers')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to Update Offer status as Inactive if same offers name exists & created by Customer
    public function updateOffers($offerTitle) {
        return Offer::where('offer_title', "=", $offerTitle);
    }

    /**
     * Update offer image.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Offer $offer
     * @return void
     */
    public function updateOfferImage($inputs, $offer) {
        if (!empty($inputs['thefile'])) {
            //unlink old file
            if (!empty($offer->offer_image)) {
                File::Delete(public_path() . ImageHelper::getOfferUploadFolder($offer->id) . $offer->offer_image);
            }
            $imageOptimizer = new ImageOptimizer;
            $offer->offer_image = ImageHelper::uploadOfferImage($inputs['thefile'], $offer, $imageOptimizer);
            $offer->save();
        } else if (isset($inputs['remove']) && $inputs['remove'] == 'remove') {
            $offer->offer_image = '';
            $offer->save();
        } else {
            $offer->save();
        }
    }

}
