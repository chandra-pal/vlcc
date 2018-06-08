<?php namespace Modules\Admin\Http\Requests\Auth;

use Modules\Admin\Http\Requests\Request;

class EmailPasswordLinkRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
        ];
    }
}
