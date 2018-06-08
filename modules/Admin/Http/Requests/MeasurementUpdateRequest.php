<?php

/**
 * The class for handling validation requests from MeasurementController::Update()
 * 
 * 
 * @author Gauri Deshmukhe <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MeasurementUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        $id = $this->measurement->id;
        return [
            'title' => 'required|max:50|regex:/^(?![\W\s]+$).+$/m|unique:measurements,title,' . $id, //alphaSpaces
            'meaning' => 'required|regex:/^(?![\W\s]+$).+$/m',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'title.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/measurement.measurement-title')]),
            'title.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/measurement.measurement-title'), 'number' => '50']),
            'title.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/measurement.measurement-title')]),
            'title.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/measurement.title')]),
            'meaning.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/measurement.meaning')]),
            'meaning.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/measurement.meaning'), 'number' => '20']),
            'meaning.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/measurement.meaning')]),
            'status.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/faq-category.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/faq-category.status')]),
        ];
    }

    public function sanitize() {
        $input = $this->all();

        $input['title'] = filter_var($input['title'], FILTER_SANITIZE_STRING);
        $input['meaning'] = filter_var($input['meaning'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->measurements->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
