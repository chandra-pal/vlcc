<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

    use DispatchesJobs,
        ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($except = []) {
        $this->middleware('authAdmin', $except);
    }

}
