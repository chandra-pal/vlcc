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

class SkinHairAnalysisCreateRequest extends Request {

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
            'hyperpigmentation_type' => 'sometimes|regex:' . $regex,
            'hyperpigmentation_size' => 'sometimes|regex:' . $regex,
            'hyperpigmentation_depth' => 'sometimes|regex:' . $regex,
            'scars_depth' => 'sometimes|regex:' . $regex,
            'scars_size' => 'sometimes|regex:' . $regex,
            'fine_lines_and_wrinkles' => 'sometimes|regex:' . $regex,
            'skin_curvature' => 'sometimes|regex:' . $regex,
            'hair_density' => 'sometimes|regex:' . $regex,
            'history_of_allergy' => 'sometimes|regex:' . $regex.'|max:255',
            'conclusion' => 'sometimes|regex:' . $regex.'|max:255',
            'skin_and_hair_specialist_name' => 'sometimes|regex:' . $regex,
        ];
    }

    public function messages() {
        return [
            'other.hyperpigmentation_type' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.hyperpigmentation-type')]),
            'hyperpigmentation_size.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.hyperpigmentation-size')]),
            'hyperpigmentation_depth.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.hyperpigmentation-depth')]),
            'scars_depth.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.scars-depth')]),
            'scars_size.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.scars-size')]),
            'fine_lines_and_wrinkles.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.fine-lines-wrinkles')]),
            'skin_curvature.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.skin-curvature')]),
            'hair_density.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.hair-density')]),
            //'history_of_allergy.max' => 'History of Allergy to any Drug / Cosmetic should not contain more than 255 characters.',
            'history_of_allergy.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.allergy-history')]),
            //'conclusion.max' => 'Conclusion should not contain more than 255 characters.',
            'conclusion.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.conclusion')]),
            'skin_and_hair_specialist_name.regex' => trans('admin::messages.error-regex-alphabets', ['name' => trans('admin::controller/cpr.name')])
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
