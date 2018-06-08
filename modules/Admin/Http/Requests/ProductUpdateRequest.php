<?php

/**
 * The class for handling validation requests from ProductController::Update()
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class ProductUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $id = $this->products->id;
        return [
            'product_title' => 'required|min:2|max:50|unique:products,product_title,' . $id,
            'product_description' => 'required',
            'product_image' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            'product_detail_page_url' => 'required|min:2|max:255|url',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'product_title.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.products-title')]),
            'product_title.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/products.products-title'), 'number' => '2']),
            'product_title.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/products.products-title'), 'number' => '255']),
            'product_title.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/products.products-title')]),
            'product_description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.product-description')]),
            'product_detail_page_url.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.product-detail-page-url')]),
            'product_detail_page_url.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/products.product_detail-page-url'), 'number' => '2']),
            'product_detail_page_url.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/products.product_detail-page-url'), 'number' => '255']),
            'product_detail_page_url.alphaSpaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/products.product_detail-page-url')]),
            'product_image.image' => trans('admin::messages.error-image', ['name' => trans('admin::controller/products.product-image')]),
            'product_image.mimes' => trans('admin::messages.mimes-name', ['name' => trans('admin::controller/products.product-image')]),
            'product_image.max' => trans('admin::messages.max-file-size-name', ['name' => trans('admin::controller/products.product-image')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/products.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();
        $input['product_title'] = filter_var($input['product_title'], FILTER_SANITIZE_STRING);
        $input['product_description'] = filter_var($input['product_description'], FILTER_SANITIZE_STRING);
        $input['product_detail_page_url'] = filter_var($input['product_detail_page_url'], FILTER_SANITIZE_STRING);
        if (Auth::guard('admin')->check()) {
            $input['updated_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
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

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->products->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
