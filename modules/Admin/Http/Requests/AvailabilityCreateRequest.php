<?php
/**
 * The class for handling validation requests from AvailabilityController::store()
 * 
 * 
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class AvailabilityCreateRequest extends Request
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
            'availability_date' => 'required', 
            'start_time' => 'required', 
            'end_time' => 'required', 
            'break_time' => 'required',             
            'carry_forwarded' => 'numeric',
            'carry_forwarded_days' => 'numeric'
        ];
    }

    public function messages()
    {
        return [
            'availability_date.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.availability-date')]),
            'start_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.start-time')]),
            'end_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.end-time')]),
            'break_time.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.break-time')]),
            'carry_forwarded.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/availability.carry-forwarded')]),
            'carry_forwarded_days.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/availability.carry-forwarded_days')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        if (Auth::guard('admin')->check()) {
            $input['dietician_id'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
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
