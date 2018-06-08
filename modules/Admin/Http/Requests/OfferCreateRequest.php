<?php

/**
 * The class for handling validation requests from OfferController::store()
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Modules\Admin\Http\Requests\Rule;
use Auth;

class OfferCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'offer_title' => 'required|regex:/^(?![\W\s]+$).+$/m|min:2|max:50|unique:offers',
            'offer_description' => 'required',
            'offer_image' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            'offer_detail_page_url' => 'required|min:2|url',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'offer_title.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/offers.offers-title')]),
            'offer_title.regex' => trans('admin::messages.error-regex-all', ['name' => trans('admin::controller/offers.offers-title')]),
            'offer_title.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/offers.offers-title'), 'number' => '2']),
            'offer_title.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/offers.offers-title'), 'number' => '255']),
            'offer_title.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/offers.offers-title')]),
            'offer_description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/offers.offer-description')]),
            'offer_detail_page_url.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/offers.offer-detail-page-url')]),
            'offer_detail_page_url.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/offers.offer-detail-page-url'), 'number' => '2']),
            'offer_image.image' => trans('admin::messages.error-image', ['name' => trans('admin::controller/offers.offer-image')]),
            'offer_image.mimes' => trans('admin::messages.mimes-name', ['name' => trans('admin::controller/offers.offer-image')]),
            'offer_image.max' => trans('admin::messages.max-file-size-name', ['name' => trans('admin::controller/offers.offer-image')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/offers.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/offers.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        
        $input['offer_title'] = trim($input['offer_title']);
        $input['offer_description'] = trim($input['offer_description']);
        
        $input['offer_title'] = filter_var($input['offer_title'], FILTER_SANITIZE_STRING);
        $input['offer_description'] = filter_var($input['offer_description'], FILTER_SANITIZE_STRING);
        $input['offer_detail_page_url'] = filter_var($input['offer_detail_page_url'], FILTER_SANITIZE_STRING);

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
