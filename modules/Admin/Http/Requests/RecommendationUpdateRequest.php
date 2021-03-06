<?php

/**
 * The class for handling validation requests from RecommendationController::Update()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class RecommendationUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $id = $this->recommendation->id;
        return [
            'message_type' => 'required|numeric', //alphaSpaces
            'message_text' => 'required|max:320|regex:/^[0-9A-Za-z\s\,.@+=!_-]+$/',
        ];
    }

    public function messages() {
        return [
            'message_type.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/recommendation.recommendation-type')]),
            'message_type.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/recommendation.recommendation-type')]),
            'message_text.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/recommendation.recommendation-text')]),
            'message_text.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/recommendation.recommendation-text'), 'number' => '320']),
            'message_text.regex' => trans('admin::messages.error-regex', ['name' => trans('admin::controller/recommendation.recommendation-text')]),
            'message_text.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/recommendation.recommendation-text')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['message_type'] = filter_var($input['message_type'], FILTER_SANITIZE_NUMBER_INT);
        $input['message_text'] = filter_var($input['message_text'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->member_notifications->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
