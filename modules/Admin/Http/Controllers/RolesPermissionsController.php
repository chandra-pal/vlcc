<?php namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\RolesRepository,
    Request,
    DB,
    Input;

class RolesPermissionsController extends Controller {

    /**
     * The RolesRepository instance.
     *
     * @var Modules\Admin\Repositories\RolesRepository
     */
    protected $roles_gestion;

    /**
     * Create a new RolesController instance.
     *
     * @param  Modules\Admin\Repositories\AdminRolesRepository $roles_gestion
     * @return void
     */
    public function __construct(RolesRepository $roles_gestion)
    {
        $this->roles_gestion = $roles_gestion;
    }

    /**
     * List all the data
     *
     * @return type
     */
    public function index()
    {
        $results = $this->roles_gestion->all();
        if (Request::ajax())
        {
            return response('Unauthorized.', 401);
        } else
        {
            return view('admin::roles-permissions.index', compact('results'));
        }
    }
}
