<?php
/**
 * The class for handling validation requests 
 * 
 * 
 * @author Nilesh Pangul <nileshp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class IpAddressCreateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return $rules = [
            'ip_address' => 'required|ip|unique:ip_addresses',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'ip_address.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/ip-address.ipaddress')]),
            'ip_address.ip' => trans('admin::messages.error-valid', ['name' => trans('admin::controller/ip-address.ipaddress')]),
            'ip_address.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/ip-address.ipaddress')]),
            'status.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/ip-address.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['ip_address'] = filter_var($input['ip_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        $input['status'] = filter_var($input['status'], FILTER_SANITIZE_NUMBER_INT);
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
