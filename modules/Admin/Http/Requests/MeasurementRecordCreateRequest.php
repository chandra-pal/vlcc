<?php

/**
 * The class for handling validation requests from MeasurementController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MeasurementRecordCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $regex = "/^([a-zA-Z ']*)$/";
        return [
            'recorded_date' => 'required',
            'neck' => 'required|between:1,250.00',
            'chest' => 'required|between:1,250.00',
            'arms' => 'required|between:1,250.00',
            'tummy' => 'required|between:1,250.00',
            'waist' => 'required|between:1,250.00',
            'hips' => 'required|between:1,250.00',
            'thighs' => 'required|between:1,250.00',
            'total_cm_loss' => 'required',
            'therapist_name' => 'required|regex:' . $regex,
        ];
    }

    public function messages() {
        return [
            'recorded_date.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.recorded-date')]),
            'neck.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.neck')]),
            'neck.between' => 'Please enter Neck (in cm) between 1 to 250.00',
            'chest.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.chest')]),
            'chest.between' => 'Please enter Chest (in cm) between 1 to 250.00',
            'arms.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.arms')]),
            'arms.between' => 'Please enter Arms (in cm) between 1 to 250.00',
            'tummy.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.tummy')]),
            'tummy.between' => 'Please enter Tummy (in cm) between 1 to 250.00',
            'waist.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.waist')]),
            'waist.between' => 'Please enter Waist (in cm) between 1 to 250.00',
            'hips.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.hips')]),
            'hips.between' => 'Please enter Hips (in cm) between 1 to 250.00',
            'thighs.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.thighs')]),
            'thighs.between' => 'Please enter Thighs (in cm) between 1 to 250.00',
            'total_cm_loss.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.total-cm-loss')]),
            'therapist_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/cpr.therapist-name')]),
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
