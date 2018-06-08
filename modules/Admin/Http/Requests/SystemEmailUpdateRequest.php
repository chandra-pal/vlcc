<?php
/**
 * The class for handling validation requests from SystemEmailController::store()
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;
use HTML;

class SystemEmailUpdateRequest extends Request
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
            'name' => 'required|alphaDash|max:255|unique:system_emails,name,' . $this->system_emails->id,
            'description' => 'required|max:255',
            'email_to' => 'email_multi',
            'email_cc' => 'email_multi',
            'email_bcc' => 'email_multi',
            'email_from' => 'addr_spec_email|max:100',
            'subject' => 'required|max:255',
            'email_type' => 'required|numeric',
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/system-email.name')]),
            'name.alphaDash' => trans('admin::messages.error-alpha-dash', ['name' => trans('admin::controller/system-email.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/system-email.name'), 'number' => '255']),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/system-email.name')]),
            'description.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/system-email.description')]),
            'description.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/system-email.description'), 'number' => '255']),
            'email_to' => trans('admin::messages.error-email-multi', ['name' => trans('admin::controller/system-email.email-to')]),
            'email_cc' => trans('admin::messages.error-email-multi', ['name' => trans('admin::controller/system-email.email-cc')]),
            'email_bcc' => trans('admin::messages.error-email-multi', ['name' => trans('admin::controller/system-email.email-bcc')]),
            'email_from' => trans('admin::messages.error-addr-spec-email', ['name' => trans('admin::controller/system-email.email-from')]),
            'email_from.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/system-email.email-from'), 'number' => '100']),
            'subject.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/system-email.subject')]),
            'subject.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/system-email.subject'), 'number' => '255']),
            'email_type.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/system-email.email-type')]),
            'email_type.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/system-email.email-type')]),
            'status' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/system-email.status')]),
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
        $input['subject'] = filter_var($input['subject'], FILTER_SANITIZE_STRING);
        $input['email_type'] = filter_var($input['email_type'], FILTER_SANITIZE_NUMBER_INT);
        if (Auth::guard('admin')->check()) {
            $input['updated_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        }
        $input['text1'] = HTML::entities($input['text1']);
        $input['text2'] = HTML::entities($input['text2']);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->system_emails->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
