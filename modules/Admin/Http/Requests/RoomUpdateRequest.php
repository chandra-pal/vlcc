<?php

/**
 * The class for handling validation requests from RoomController::update()
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class RoomUpdateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->sanitize();
        return [
            'name' => 'required|max:200|regex:/^(?![0-9]*$)[a-zA-Z0-9\s\-()\/ ]+$/|unique:rooms,name,' . $this->rooms->id . ',id,center_id,' . $this->rooms->center_id,
            'center_id' => 'required',
            'room_type' => 'required|numeric',
            'status' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'center_id.required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/room.center')]),
            'name.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/room.name')]),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/room.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/room.name'), 'number' => '200']),
            'name.regex' => trans('admin::messages.error-regex-non-numb', ['name' => trans('admin::controller/room.name')]),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/room.name')]),
            'room_type.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/room.room_type')]),
            'room_type.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/room.room_type')]),
            'status.required' => trans('admin::messages.error-required', ['name' => trans('admin::controller/room.status')]),
            'status.numeric' => trans('admin::messages.error-numeric-id', ['name' => trans('admin::controller/room.status')]),
        ];
    }

    /**
     * Sanitize all input fieds and replace
     */
    public function sanitize() {
        $input = $this->all();

        $input['center_id'] = filter_var($input['center_id'], FILTER_SANITIZE_NUMBER_INT);
        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->rooms->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }

}
