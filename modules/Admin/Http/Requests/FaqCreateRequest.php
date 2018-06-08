<?php
/**
 * The class for handling validation requests from FaqController::store()
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class FaqCreateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        // dd($this);
        return [
            'faq_category_id' => 'required|numeric',
            'question' => 'required|unique:faqs',
            'answer' => 'required',
            'position' => 'required|unique:faqs,position,null,id,faq_category_id,' . $this->all()['faq_category_id'],
            'status' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'faq_category_id.required' => trans('admin::messages.error-required-select', ['name' => trans('admin::controller/faq-category.faq-cat')]),
            'faq_category_id.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/faq-category.faq-cat')]),
            'question.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq.question')]),
            'question.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/faq.question')]),
            'answer.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq.answer')]),
            'position.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq.position')]),
            'position.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/faq.position')]),
            'position.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/faq.position')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/faq.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize()
    {
        $input = $this->all();

        $input['faq_category_id'] = filter_var($input['faq_category_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['question'] = filter_var($input['question'], FILTER_SANITIZE_STRING);
        $input['answer'] = filter_var($input['answer'], FILTER_SANITIZE_STRING);
        $input['position'] = filter_var($input['position'], FILTER_SANITIZE_NUMBER_INT);
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
