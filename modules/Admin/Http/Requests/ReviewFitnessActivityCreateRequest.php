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

class ReviewFitnessActivityCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();

        $pulse_limit = config('settings.APP_PULSE');
        $regex = "/.*[a-z]+.*/i";
        return [
            'static_posture_score' => 'sometimes|integer|digits:1|between:1,4',
            'sit_and_reach_test' => 'numeric',
            'right_shoulder_flexibility' => 'numeric',
            'left_shoulder_flexibility' => 'numeric',
            'pulse' => 'sometimes|integer|between:1,' . $pulse_limit,
            'specific_activity_advice' => 'sometimes|regex:' . $regex,
            'specific_activity_duration' => 'sometimes|min:1|integer',
            'physiotherapist_name' => 'sometimes|regex:' . $regex,
            'precautions_and_contraindications' => 'sometimes|regex:' . $regex,
        ];
    }

    public function messages() {
        $pulse_limit = config('settings.APP_PULSE');
        return [
            'static_posture_score.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.static-posture')]),
            'static_posture_score.digits' => trans('admin::messages.error-digit-maxlength', ['name' => trans('admin::controller/cpr.static-posture'), 'count' => '1']),
            'sit_and_reach_test.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.sit-and-reach-test')]),
            'right_shoulder_flexibility.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.shoulder-right')]),
            'left_shoulder_flexibility.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.shoulder-left')]),
            'pulse.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.pulse')]),
            'pulse.between' => trans('admin::messages.error-between', ['name' => trans('admin::controller/cpr.pulse'), 'from' => '1', 'to' => $pulse_limit]),
            'specific_activity_advice.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.activity-code')]),
            'specific_activity_duration.min' => "Activity Duration should contain at least 1 number",
            'specific_activity_duration.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/activity-duration')]),
            'physiotherapist_name.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.physiotherapist-name')]),
            'precautions_and_contraindications.regex' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.precautions-contraindications')]),
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
