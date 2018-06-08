<?php

/**
 * The class for handling validation requests from DietPlanController::store()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Validator;
use Modules\Admin\Http\Requests\Request;
use Auth;

class MemberProfileImageCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        if ($this->get('before_img_avatar') == '') {
            return [
                'before_image' => 'required|image|mimes:jpg,jpeg,gif,png|max:2048',
                'after_image' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            ];
        } else {
            return [
                'before_image' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
                'after_image' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            ];
        }
    }

    public function messages() {
        return [
            'before_image.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/member-profile-image.before-avatar')]),
            'before_image.image' => trans('admin::messages.error-image', ['name' => trans('admin::controller/member-profile-image.before-avatar')]),
            'before_image.mimes' => trans('admin::messages.mimes-name', ['name' => trans('admin::controller/member-profile-image.before-avatar')]),
            'before_image.max' => trans('admin::messages.max-file-size-name', ['name' => trans('admin::controller/member-profile-image.before-image')]),
            'after_image.image' => trans('admin::messages.error-image', ['name' => trans('admin::controller/member-profile-image.after-avatar')]),
            'after_image.mimes' => trans('admin::messages.mimes-name', ['name' => trans('admin::controller/member-profile-image.before-avatar')]),
            'after_image.max' => trans('admin::messages.max-file-size-name', ['name' => trans('admin::controller/member-profile-image.before-avatar')]),
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
