<?php
/**
 * The class for handling validation requests from LocationsController::store()
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class LocationsCreateRequest extends Request
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
            'country_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'location' => 'required|max:200|unique:locations,location,null,id,city_id,' . $this->all()['city_id'], //alphaSpaces
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/locations.country')]),
            'country_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/locations.country')]),
            'state_id.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/locations.state')]),
            'state_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/locations.state')]),
            'city_id.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/city.state')]),
            'city_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/locations.state')]),
            'location.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/locations.location')]),
            'location.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/locations.location')]),
            'location.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/locations.location'), 'number' => '200']),
            'location.unique' => trans('admin::messages.error-taken-location', ['name' => trans('admin::controller/locations.location')]),
            'status.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/locations.location')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/locations.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['country_id'] = filter_var($input['country_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['state_id'] = filter_var($input['state_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['city_id'] = filter_var($input['city_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['location'] = filter_var($input['location'], FILTER_SANITIZE_STRING);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);
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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $status = Auth::guard('admin')->user()->can($action['as'], 'store');
        if (empty($status)) {
            abort(403);
        }
        return true;
    }
}
