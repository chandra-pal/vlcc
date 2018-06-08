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

class DietaryAssessmentCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $this->sanitize();
        $data = $this->all();
        $regex = "/.*[a-z]+.*/i";
        $meal_limit = config('settings.APP_MEALS_PER_DAY');
        $eat_out_limit = config('settings.APP_EAT_OUT_PER_WEEK');
        $calories_limit = config('settings.APP_CALORIES_LIMIT');
        $cho_limit = config('settings.APP_DIET_CHO');
        $protein_limit = config('settings.APP_DIET_PROTEIN');
        $fat_limit = config('settings.APP_DIET_FAT');
        if (isset($data['smoking']) && $data['smoking'] == 1) {
            return [
                'smoking_frequency' => 'required'
            ];
        }

        if (isset($data['alcohol']) && $data['alcohol'] == 1) {
            return [
                'alcohol_frequency' => 'required'
            ];
        }
        return [
            'food_allergy' => 'sometimes|regex:' . $regex,
            'smoking' => 'sometimes',
            'meals_per_day' => 'sometimes|integer|between:1,' . $meal_limit,
            'eat_out_per_week' => 'sometimes|integer',
            'fasting' => 'sometimes|integer|between:0,7',
            'alcohol' => 'sometimes',
            'diet_total_calories' => 'sometimes|integer|between:1,' . $calories_limit,
            'diet_cho' => 'sometimes|integer|between:1,' . $cho_limit,
            'diet_protein' => 'sometimes|integer|between:1,' . $protein_limit,
            'diet_fat' => 'sometimes|integer|between:1,' . $fat_limit,
            'remark' => 'sometimes|regex:' . $regex,
            'wellness_counsellor_name' => 'sometimes|regex:' . $regex,
        ];
    }

    public function messages() {
        $meal_limit = config('settings.APP_MEALS_PER_DAY');
        $eat_out_limit = config('settings.APP_EAT_OUT_PER_WEEK');
        $calories_limit = config('settings.APP_CALORIES_LIMIT');
        $cho_limit = config('settings.APP_DIET_CHO');
        $protein_limit = config('settings.APP_DIET_PROTEIN');
        $fat_limit = config('settings.APP_DIET_FAT');
        return [
            'food_allergy.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.food-allergy')]),
            'alcohol_frequency.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.alcohol-frequency')]),
            'smoking_frequency.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/cpr.smoking-frequency')]),
            'meals_per_day.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.meals-per-day')]),
            'meals_per_day.between' => 'Please enter Meals / Day between 1 to ' . $meal_limit . '.',
            'eat_out_per_week.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.eat-out-per-week')]),
            'fasting.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.fasting')]),
            'fasting.between' => 'Please enter Fating / Week between 0 to 7',
            'diet_total_calories.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.diet-total-calories')]),
            'diet_total_calories.between' => 'Please enter Calories between 1 to ' . $calories_limit . '.',
            'diet_cho.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.diet-cho')]),
            'diet_cho.between' => 'Please enter Diet CHO between 1 to ' . $cho_limit . '.',
            'diet_protein.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.diet-protein')]),
            'diet_protein.between' => 'Please enter Diet Protein between 1 to ' . $protein_limit . '.',
            'diet_fat.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/cpr.diet-fat')]),
            'diet_fat.between' => 'Please enter Diet Fat between 1 to ' . $fat_limit . '.',
            'remark.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.remarks')]),
            'wellness_counsellor_name.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.wellness-counsellor-name')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['food_allergy'] = filter_var($input['food_allergy'], FILTER_SANITIZE_STRING);
        $input['meals_per_day'] = filter_var($input['meals_per_day'], FILTER_SANITIZE_NUMBER_INT);
        $input['eat_out_per_week'] = filter_var($input['eat_out_per_week'], FILTER_SANITIZE_NUMBER_INT);
        $input['fasting'] = filter_var($input['fasting'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_total_calories'] = filter_var($input['diet_total_calories'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_cho'] = filter_var($input['diet_cho'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_protein'] = filter_var($input['diet_protein'], FILTER_SANITIZE_NUMBER_INT);
        $input['diet_fat'] = filter_var($input['diet_fat'], FILTER_SANITIZE_NUMBER_INT);
        $input['remark'] = filter_var($input['remark'], FILTER_SANITIZE_STRING);
        $input['wellness_counsellor_name'] = filter_var($input['wellness_counsellor_name'], FILTER_SANITIZE_STRING);

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
