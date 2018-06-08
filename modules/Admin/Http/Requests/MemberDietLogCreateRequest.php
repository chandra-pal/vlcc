<?php

/**
 * The class for handling validation requests from MemberDietLogController::store()
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberDietLogCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $serving = config('settings.APP_SERVING_SIZE_LIMIT');
        foreach ($this->request->get('food_id') as $key => $val) {
            $rules['food_id.' . $key] = 'required';
        }

        foreach ($this->request->get('servings_recommended') as $key => $val) {
            $rules['servings_recommended.' . $key] = 'required|integer|between:1,' . $serving;
        }

        return $rules;
    }

    public function messages() {
        return [
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
