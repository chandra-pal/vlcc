<?php

/**
 * The repository class for managing OfferRecommendation specific actions.
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\OfferRecommendation;
use Modules\Admin\Models\Offer;
use Exception;
use Route;
use Log;
use Cache;
use Auth;
use Session;
use Illuminate\Http\Request;

class OfferRecommendationRepository extends BaseRepository {

    /**
     * Create a new OfferRecommendationRepository instance.
     *
     * @param  Modules\Admin\Models\OfferRecommendation $offerRecommendation
     * @return void
     */
    public function __construct(OfferRecommendation $offerRecommendation) {
        $this->model = $offerRecommendation;
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
                $response = Cache::tags(OfferRecommendation::table())->remember($cacheKey, $this->ttlCache, function() {

                    return OfferRecommendation::with('offer')->where([['member_id', '=', Session::get('member_id')], ['created_by', '=', Auth::guard('admin')->user()->id]])
                                    ->orderBy('id', 'DESC')
                                    ->get();
                });
            } else {
                return OfferRecommendation::with('offer')->where([['member_id', '=', ''], ['created_by', '=', Auth::guard('admin')->user()->id]])
                                ->orderBy('id', 'DESC')
                                ->get();
            }
        } else {
            //Cache::tags not suppport with files and Database
            $response = Cache::tags(OfferRecommendation::table())->remember($cacheKey, $this->ttlCache, function() {
                return OfferRecommendation::with('offer')->where([['created_by', '=', Auth::guard('admin')->user()->id]])
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
    public function getOfferList() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Offer::table())->remember($cacheKey, $this->ttlCache, function() {
            return Offer::whereStatus(1)->orderBy('id')->lists('offer_title', 'id');
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
            foreach ($inputs['offer_id'] as $offer_Key => $offer_value) {
                $offerRecommendation = new $this->model;
                $offerRecommendation->offer_id = $inputs['offer_id'][$offer_Key];

                if (isset($inputs['member_id']) && $inputs['member_id'] != '0') {
                    $offerRecommendation->member_id = $inputs['member_id'];
                } elseif ($user_type_id == '1') {
                    $offerRecommendation->member_id = '0';
                }
                $offerRecommendation->created_by = Auth::guard('admin')->user()->id;
                $save[] = $offerRecommendation->save();
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an OfferRecommendation.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\OfferRecommendation $offerRecommendation
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $offerRecommendation) {
        try {

            foreach ($inputs['offer_id'] as $offer_Key => $offer_value) {
                $offerRecommendation->offer_id = $inputs['offer_id'][$offer_Key];
                $offerRecommendation->status = $inputs['status'];
                $offerRecommendation->updated_by = $inputs['updated_by'];
                $save[] = $offerRecommendation->save();
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
