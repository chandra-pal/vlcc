<?php
/**
 * The class for handling validation requests from UserTypeController::update()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class UserTypeUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        return [
            'name' => 'required|max:255|unique:user_types,name,' . $this->user_type->id, //alphaSpaces
            'description' => 'max:255', //alphaSpaces
            'priority' => 'required|numeric',
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.name')]),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/usertype.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/usertype.name'), 'number' => '255']),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/usertype.name')]),
            'description.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/usertype.description')]),
            'description.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/usertype.description'), 'number' => '255']),
            'priority.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.priority')]),
            'priority.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/usertype.priority')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertype.status')]),
            'status.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/usertype.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
        $input['priority'] = filter_var($input['priority'], FILTER_SANITIZE_NUMBER_INT);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);
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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $is_edit = Auth::guard('admin')->user()->can($action['as'], 'edit');
        $own_edit = Auth::guard('admin')->user()->can($action['as'], 'own_edit');

        if ($is_edit == 1 || (!empty($own_edit) && ($this->user_type->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
