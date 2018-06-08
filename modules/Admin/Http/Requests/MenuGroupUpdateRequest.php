<?php
/**
 * The class for handling validation requests from MenuGroupController::Update()
 *
 *
 * @author Prashant Birajdar <prashantb@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Requests;

use Modules\Admin\Http\Requests\Request;
use Auth;

class MenuGroupUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();
        $id = $this->menu_group->id;
        return [
            'name' => 'required|max:150|unique:menu_groups,name,' . $id, //alphaSpaces
            'position' => 'required|numeric|unique:menu_groups,position,' . $id
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/menu-group.name')]),
            'name.max' => trans('admin::messages.error-maxlength-number', ['name' => trans('admin::controller/menu-group.name'), 'number' => '150']),
            'name.alpha_spaces' => trans('admin::messages.error-alpha-spaces', ['name' => trans('admin::controller/menu-group.name')]),
            'name.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/menu-group.name')]),
            'position.required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/menu-group.position')]),
            'position.numeric' => trans('admin::messages.error-numeric', ['name' => trans('admin::controller/menu-group.position')]),
            'position.unique' => trans('admin::messages.error-taken', ['name' => trans('admin::controller/menu-group.position')]),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['position'] = filter_var($input['position'], FILTER_SANITIZE_NUMBER_INT);
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

        if ($is_edit == 1 || (!empty($own_edit) && ($this->menu_groups->created_by == Auth::guard('admin')->user()->id))) {
            return true;
        } else {
            abort(403);
        }
    }
}
