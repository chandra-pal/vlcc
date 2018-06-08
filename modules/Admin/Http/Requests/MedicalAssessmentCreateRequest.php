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

class MedicalAssessmentCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $this->sanitize();
        $input = $this->all();
        $regex = "/.*[a-z]+.*/i";

        return [
            'other' => 'sometimes|regex:' . $regex,
            'physical_finding' => 'sometimes|regex:' . $regex,
            'gynae_obstetrics_history' => 'sometimes|regex:' . $regex,
            'systemic_examination' => 'sometimes|regex:' . $regex,
            'past_mediacl_history' => 'sometimes|regex:' . $regex,
            'family_history_of_diabetes_obesity' => 'sometimes|regex:' . $regex,
            'detailed_history' => 'sometimes|regex:' . $regex,
            'treatment_history' => 'sometimes|regex:' . $regex,
            'suggested_investigation' => 'sometimes|regex:' . $regex,
            'doctors_name' => 'sometimes|regex:' . $regex,
        ];
    }

    public function messages() {
        return [
            'other.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.other')]),
            'physical_finding.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.physical-finding')]),
            'gynae_obstetrics_history.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.gyane-obstetrics')]),
            'systemic_examination.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.systemic-examination')]),
            'past_mediacl_history.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.past-medical-history')]),
            'family_history_of_diabetes_obesity.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.family-history')]),
            'detailed_history.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.detailed-history')]),
            'treatment_history.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.treatment-history')]),
            'suggested_investigation.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.suggested-investigation')]),
            'doctors_name.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.doctor-name')])
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
