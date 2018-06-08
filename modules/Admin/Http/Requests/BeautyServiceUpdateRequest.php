<?php
/**
 * The class for handling validation requests from BeautyServiceController::update()
 * 
 * 
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class BeautyServiceUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        return [
            //'service_name' => 'required|max:100|alphaSpaces|unique:beauty_services,service_name,' .$this->route('beauty-service')['id'], //alphaSpaces',
            'service_name' => 'required|max:100|unique:beauty_services,service_name,' .$this->beauty_service->id, //alphaSpaces',
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'service_name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
           // 'service_name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
            'service_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/beauty-service.beauty-service'), 'number' => '100']),
            'service_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/beauty-service.beauty-service')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/beauty-service.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/beauty-service.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['service_name'] = filter_var($input['service_name'], FILTER_SANITIZE_STRING);
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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->beauty_service->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
