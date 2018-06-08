<?php
/**
 * The class for managing user assigned links specific actions.
 * 
 * 
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\LinksRepository,
    Modules\Admin\Http\Requests\UserTypeLinksCreateRequest,
    Modules\Admin\Repositories\UserTypeRepository,
    Modules\Admin\Repositories\LinkCategoryRepository;

class UserTypeLinksController extends Controller
{

    /**
     * The RolesRepository instance.
     *
     * @var Modules\Admin\Repositories\LinksRepository
     */
    protected $userTypeLinks;
    protected $usertype;

    /**
     * Create a new linkRepository instance.
     *
     * @param  Modules\Admin\Repositories\LinksRepository $linksRepositories
     * @return void
     */
    public function __construct(LinksRepository $linksRepositories, UserTypeRepository $usertype, LinkCategoryRepository $linkCategoryRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->linkRepository = $linksRepositories;
        $this->linkCategory = $linkCategoryRepository;
        $this->usertype = $usertype;
    }

    /**
     * List all the data
     *
     * @return type
     */
    public function index()
    {
        $userTypeCategoryLinks = [];
        $links = $this->linkCategory->getAllLinksByCategory()->groupBy('link_category_id')->first();
        $userTypes = $this->usertype->listUserTypeData()->toArray();
        return view('admin::usertype-links.index', compact('links', 'userTypes','userTypeCategoryLinks'));
    }

    /**
     * Stores all the data
     *
     * @return type
     */
    public function store(UserTypeLinksCreateRequest $request)
    {
        $response = $this->usertype->saveLinks($request->links, $request->type_id);
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Modules\Admin\Models\userType
     * @return Response
     */
    public function edit($usertypeLinks)
    {
        $userType = $usertypeLinks->first();
        $links = $this->linkCategory->getAllLinksByCategory()->groupBy('link_category_id')->first();
        $userTypeLinksData = $this->linkRepository->listTypewiseLinksData($userType->id);
        $userTypeCategoryLinks = $usertypeLinks->lists('Links')->first()->groupBy('link_category_id')->toArray();
        $response['success'] = true;
        $response['form'] = view('admin::usertype-links.form', compact('userTypeLinksData', 'links', 'userTypeCategoryLinks'))->render();

        return response()->json($response);
    }
}
