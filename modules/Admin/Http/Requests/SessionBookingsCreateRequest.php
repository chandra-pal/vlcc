<?php
/**
 * The class for handling validation requests from SessionBookingsController::store()
 * 
 * 
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class SessionBookingsCreateRequest extends Request
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
            'member_id' => 'required|integer',
            'package_id' => 'required|integer',
            'session_date' => 'required|date',
            'session_comment' => 'min:2|max:350'
        ];
    }

    public function messages()
    {
        return [
            'member_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.select-customer')]),
            //'member_id.integer' => trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.select-customer')]),
            'package_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.select-package')]),
            //'package_id.integer' => trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.session-bookings')]),
            'session_date.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/session-bookings.session-dat')]),
            'session_comment.min' => trans('admin::messages.error-minlength-number', ['name' => trans('admin::controller/session-bookings.session-comment'), 'number' => '2']),
            'session_comment.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/session-bookings.session-comment'), 'number' => '350'])
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['member_id'] = filter_var($input['member_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['package_id'] = filter_var($input['package_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['session_date'] = date('Y-m-d', strtotime($input['session_date']));
        $input['start_time'] = date('H:i', strtotime($input['start_time']));
        $input['end_time'] = date('H:i', strtotime($input['end_time']));
        $input['session_comment'] = filter_var($input['session_comment'], FILTER_SANITIZE_STRING);

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
    public function authorize()
    {
        $action = $this->route()->getAction();

        $status = Auth::guard('admin')->user()->can($action['as'], 'store');
        if (empty($status)) {
            abort(403);
        }
        return true;
    }
}
