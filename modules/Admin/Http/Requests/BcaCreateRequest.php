<?php

/**
 * The class for handling validation requests from CPRController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class BcaCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'recorded_date' => 'required',
            'basal_metabolic_rate' => 'required',
            'fat_weight' => 'required',
            'fat_percent' => 'required',
            'lean_body_mass_weight' => 'required',
            'lean_body_mass_percent' => 'required',
            'water_weight' => 'required',
            'water_percent' => 'required',
            'target_weight' => 'required',
            'target_fat_percent' => 'required',
            'body_mass_index' => 'required',
            //'visceral_fat_level' => 'required',
        ];
    }

    public function messages() {
        return [
            'recorded_date.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.recorded-date')]),
            'basal_metabolic_rate.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.basal-metabolic-rate')]),
            'fat_weight.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.fat-weight')]),
            'fat_percent.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.fat-percent')]),
            'lean_body_mass_weight.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.lean-body-mass-weight')]),
            'lean_body_mass_percent.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.lean-body-mass-percent')]),
            'water_weight.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.water-weight')]),
            'water_percent.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.water-percent')]),
            'target_weight.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.target-weight')]),
            'target_fat_percent.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.target-fat-percent')]),
            'body_mass_index.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.body-mass-index')]),
            //'visceral_fat_level.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.visceral-fat-level')]),
        ];
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
