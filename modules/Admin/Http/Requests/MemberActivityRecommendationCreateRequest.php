<?php

/**
 * The class for handling validation requests from RecommendationController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberActivityRecommendationCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $limit = config('settings.APP_CALORIES_LIMIT');
        return [
            'activity_type_id' => 'required|numeric', //alphaSpaces
            'recommendation_date' => 'required',
            'duration' => 'required|min:1|integer',
            'calories_recommended' => 'required|min:1|integer|between:1,' . $limit,
        ];
    }

    public function messages() {
        return [
            'activity_type_id.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.activity-type')]),
            'activity_type_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/member-activity-recommendation.activity-type')]),
            'recommendation_date.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.recommendation-date-time')]),
            'duration.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.duration')]),
            'duration.min' => "Activity Duration should contain at least 1 number",
            'duration.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/member-activity-recommendation.duration')]),
            'calories_recommended.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-activity-recommendation.recommended-calories')]),
            'calories_recommended.min' => "Calories should contain at least 1 number",
            'calories_recommended.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/member-activity-recommendation.recommended-calories')]),
            'calories_recommended.between' => 'Please enter valid Calories.',
        ];
    }

    public function sanitize() {
        $input = $this->all();
        $input['activity_type_id'] = filter_var($input['activity_type_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['recommendation_date'] = filter_var($input['recommendation_date'], FILTER_SANITIZE_STRING);
        $input['duration'] = filter_var($input['duration'], FILTER_SANITIZE_NUMBER_INT);
        $input['calories_recommended'] = filter_var($input['calories_recommended'], FILTER_SANITIZE_NUMBER_INT);
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
