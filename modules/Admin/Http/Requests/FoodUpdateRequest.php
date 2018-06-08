<?php

/**
 * The class for handling validation requests from FoodController::Update()
 * 
 * 
 * @author Priyanka Deshpane <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class FoodUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $limit = config('settings.APP_CALORIES_LIMIT');
        $this->sanitize();
        $id = $this->food->id;
        return [
            'food_name' => 'required|min:2|max:50|alphaSpaces|unique:foods,food_name,' . $id, //alphaSpaces
            'measure' => 'required|min:1|max:50|regex:/^(?![\W\s]+$).+$/m',
            'calories' => 'required|min:1|integer|between:1,' . $limit,
            //'serving_size' => 'required|min:1|integer|between:1,60000',
            //'serving_unit' => 'required|min:2|max:20|alphaSpaces'
        ];
    }

    public function messages() {
        return [
            'food_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.name')]),
            'food_name.min' => "Food Name should contain at least 2 characters",
            'food_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/food.name'), 'number' => '50']),
            'food_name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/food.name')]),
            'food_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/food.name')]),
            'measure.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.measure')]),
            'measure.min' => "Measure should contain at least 1 character",
            'measure.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/food.measure'), 'number' => '50']),
            'measure.regex' => trans('admin::messages.error-regex-non-special-char', ['name' => trans('admin::controller/food.measure')]),
            'calories.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.calories')]),
            'calories.min' => "Calories should contain at least 1 number",
            'calories.max' => "Calories should not be greater than 50 numbers",
            'calories.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/food.calories')]),
            'calories.between' => 'Please enter valid Calories.',
            'serving_size.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.serving-size')]),
            'serving_size.min' => "Serving size should contain at least 1 characters",
            'serving_size.integer' => trans('admin::messages.error-integer', ['name' => trans('admin::controller/food.serving-size')]),
            'serving_size.between' => 'Please enter valid Serving Size.',
            'serving_unit.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.serving-unit')]),
            'serving_unit.min' => "Serving Unit should contain at least 2 characters",
            'serving_unit.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/food.serving-unit')]),
            'serving_unit.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/food.measure'), 'number' => '20']),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['food_name'] = filter_var($input['food_name'], FILTER_SANITIZE_STRING);
        $input['measure'] = filter_var($input['measure'], FILTER_SANITIZE_STRING);
        $input['calories'] = filter_var($input['calories'], FILTER_SANITIZE_NUMBER_INT);
        //$input['serving_size'] = filter_var($input['serving_size'], FILTER_SANITIZE_NUMBER_INT);
        //$input['serving_unit'] = filter_var($input['serving_unit'], FILTER_SANITIZE_STRING);
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
    public function authorize() {
        $action = $this->route()->getAction();

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->food->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
