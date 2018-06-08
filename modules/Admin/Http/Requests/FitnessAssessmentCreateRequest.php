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

class FitnessAssessmentCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();

        $pulse_limit = config('settings.APP_PULSE');
        $staticPostureMin = config('settings.APP_STATIC_POSTURE_MIN');
        $staticPostureMax = config('settings.APP_STATIC_POSTURE_MAX');
        $regex = "/.*[a-z]+.*/i";
        return [
            'static_posture' => 'sometimes|integer|digits:1|between:'.$staticPostureMin.','.$staticPostureMax,
            'sit_and_reach_test' => 'numeric',
            'shoulder_flexibility_right' => 'numeric',
            'shoulder_flexibility_left' => 'numeric',
            'pulse' => 'sometimes|integer|between:1,' . $pulse_limit,
            'back_problem_test' => 'sometimes',
            'current_activity_pattern' => 'sometimes|regex:' . $regex,
            'current_activity_type' => 'sometimes|regex:' . $regex,
            'current_activity_frequency' => 'sometimes|regex:' . $regex,
            'current_activity_duration' => 'sometimes|integer',
            'remark' => 'sometimes|regex:' . $regex,
            'home_care_kit' => 'sometimes',
            'physiotherapist_name' => 'sometimes|regex:' . $regex,
        ];
    }

    public function messages() {
        $pulse_limit = config('settings.APP_PULSE');
        $staticPostureMin = config('settings.APP_STATIC_POSTURE_MIN');
        $staticPostureMax = config('settings.APP_STATIC_POSTURE_MAX');
        return [
            'static_posture.numeric' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.static-posture')]),
            'static_posture.digits' => trans('admin::messages.error-digit-maxlength', ['name' => trans('admin::controller/cpr.static-posture'), 'count' => '1']),
            'static_posture.between' => trans('admin::messages.error-between', ['name' => trans('admin::controller/cpr.static-posture'), 'from' => $staticPostureMin, 'to' => $staticPostureMax]),
            'sit_and_reach_test.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.sit-and-reach-test')]),
            'shoulder_flexibility_right.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.shoulder-right')]),
            'shoulder_flexibility_left.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/cpr.shoulder-left')]),
            'pulse.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.pulse')]),
            'pulse.between' => 'Pulse rate should be between 1 to ' . $pulse_limit,
            'current_activity_pattern.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.current-activity-pattern')]),
            'current_activity_type.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.current-activity-type')]),
            'current_activity_frequency.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.current-activity-frequency')]),
            'current_activity_duration.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.current-activity-duration')]),
            'remark.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.remarks')]),
            'physiotherapist_name.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.physiotherapist-name')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['static_posture'] = filter_var($input['static_posture'], FILTER_SANITIZE_STRING);
        $input['sit_and_reach_test'] = filter_var($input['sit_and_reach_test'], FILTER_SANITIZE_NUMBER_FLOAT);

        $input['shoulder_flexibility_right'] = filter_var($input['shoulder_flexibility_right'], FILTER_SANITIZE_NUMBER_FLOAT);
        $input['shoulder_flexibility_left'] = filter_var($input['shoulder_flexibility_left'], FILTER_SANITIZE_NUMBER_FLOAT);
        $input['pulse'] = filter_var($input['pulse'], FILTER_SANITIZE_NUMBER_INT);
        $input['current_activity_pattern'] = filter_var($input['current_activity_pattern'], FILTER_SANITIZE_STRING);
        $input['current_activity_type'] = filter_var($input['current_activity_type'], FILTER_SANITIZE_STRING);
        $input['current_activity_frequency'] = filter_var($input['current_activity_frequency'], FILTER_SANITIZE_STRING);
        $input['current_activity_duration'] = filter_var($input['current_activity_duration'], FILTER_SANITIZE_STRING);
        $input['remark'] = filter_var($input['remark'], FILTER_SANITIZE_STRING);
        $input['physiotherapist_name'] = filter_var($input['physiotherapist_name'], FILTER_SANITIZE_STRING);
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
