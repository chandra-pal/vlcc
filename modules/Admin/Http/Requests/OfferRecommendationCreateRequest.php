<?php

/**
 * The class for handling validation requests from OfferRecommendationController::store()
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class OfferRecommendationCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $data = $this->all();
        $this->sanitize();
        if ($data['offer_id'][0] == "") {
            return [
                'offer_ids' => 'required',
            ];
        } else {
            return [
            ];
        }
    }

    public function messages() {
        $data = $this->all();
        if ($data['offer_id'][0] == "") {
            return [
                'offer_ids.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/offer-recommendation.offer')]),
            ];
        } else {
            return [
            ];
        }
    }

    public function sanitize() {
        $input = $this->all();

        if (Auth::guard('admin')->check()) {
            $input['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }

        $this->merge($input);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $action = $this->route()->getAction();

        $status = Auth::guard('admin')->user()->can($action['as'], 'store');
        if (empty($status)) {
            abort(403);
        }
        return true;
    }

}
