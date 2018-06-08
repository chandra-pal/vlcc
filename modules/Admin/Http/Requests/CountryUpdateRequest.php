<?php
/**
 * The class for handling validation requests from CountryController::update()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class CountryUpdateRequest extends Request
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
            'name' => 'required|max:200|unique:countries,name,' . $this->countries->id, //alphaSpaces
            'iso_code_2' => 'required|max:2|unique:countries,iso_code_2,' . $this->countries->id, //alpha
            'iso_code_3' => 'required|max:3|unique:countries,iso_code_3,' . $this->countries->id, //alpha
            'isd_code' => 'required|numeric|unique:countries,isd_code,' . $this->countries->id,
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/country.name')]),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/country.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/country.name'), 'number' => '200']),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/country.name')]),
            'iso_code_2.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/country.iso-code-2')]),
            'iso_code_2.alpha' => trans('admin::messages.error-alpha', ['name' => trans('admin::controller/country.iso-code-2')]),
            'iso_code_2.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/country.iso-code-2'), 'number' => '2']),
            'iso_code_2.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/country.iso-code-2')]),
            'iso_code_3.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/country.iso-code-3')]),
            'iso_code_3.alpha' => trans('admin::messages.error-alpha', ['name' => trans('admin::controller/country.iso-code-3')]),
            'iso_code_3.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/country.iso-code-3'), 'number' => '3']),
            'iso_code_3.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/country.iso-code-3')]),
            'isd_code.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/country.isd-code')]),
            'isd_code.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/country.isd-code')]),
            'isd_code.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/country.isd-code')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/country.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/country.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['iso_code_2'] = filter_var($input['iso_code_2'], FILTER_SANITIZE_STRING);
        $input['iso_code_3'] = filter_var($input['iso_code_3'], FILTER_SANITIZE_STRING);
        //$input['isd_code'] = filter_var($input['isd_code'], FILTER_SANITIZE_NUMBER_INT);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->countries->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
