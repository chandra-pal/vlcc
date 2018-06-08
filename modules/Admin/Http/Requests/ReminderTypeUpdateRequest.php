<?php
/**
 * The class for handling validation requests from ReminderTypeController::Update()
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class ReminderTypeUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        $id = $this->reminder_type->id;
        return [
            'type_name' => 'required|max:50|regex:/^(?![0-9]*$)[a-zA-Z0-9\s\-()\/]+$/|unique:reminder_types,type_name,' . $id, //alphaSpaces
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'type_name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/reminder-type.reminder-type')]),
            'type_name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/reminder-type.reminder-type'), 'number' => '50']),
            'type_name.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/reminder-type.reminder-type')]),
            'type_name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/reminder-type.reminder-type')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/reminder-type.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/reminder-type.status')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['type_name'] = filter_var($input['type_name'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->faq_categories->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
