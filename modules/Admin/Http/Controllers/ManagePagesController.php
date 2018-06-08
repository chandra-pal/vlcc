<?php namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Repositories\PagesRepository,
    Datatables,
    Modules\Admin\Models\Page,
    Modules\Admin\Http\Requests\PageCreateRequest,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\Auth,
    Illuminate\Support\Str,
    Modules\Admin\Http\Requests\PageUpdateRequest;

class ManagePagesController extends Controller
{

    public function __construct(PagesRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Hadle Ajax Group Action
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function groupAction(Request $request)
    {
        $response = [];
        $result = $this->repository->groupAction($request->all());
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/page.page_details')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/page.page_details')]);
        }

        return response()->json($response);
    }

    public function index()
    {
        return view('admin::pages.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getData()
    {
        $pagesList = $this->repository->data();
        //to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $pagesList = $pagesList->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($pagesList)
                ->addColumn('ids', function ($page) {
                    if (!empty(\Auth::guard('admin')->user()->hasEdit) && !empty(\Auth::guard('admin')->user()->hasDelete)) {
                        return '<input type="checkbox" name="ids[]" value="' . $page->id . '">';
                    }
                })
                ->addColumn('display_page_name', function ($page) {
                    $pageName = $page->page_name . ' [ ' . $page->slug . ' ]';
                    $page_desc = strip_tags(html_entity_decode($page->page_desc));
                    $pageName .= '<br />' . $page_desc;
                    return $pageName;
                })
                ->addColumn('display_page_url', function ($page) {
                    $page_url = URL('/') . '/' . $page->page_url;
                    return $page_url;
                })
                ->addColumn('display_page_desc', function ($page) {
                    $page_desc = strip_tags(html_entity_decode($page->page_desc));
                    return $page_desc;
                })
                ->addColumn('status', function ($page) {
                    $status = ($page->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($page) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($page->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList .= '<a href="javascript:;" data-action="edit" data-id="' . $page->id . '" class="btn btn-xs default yellow-gold margin-bottom-5 edit-form-link" id="' . $page->id . '"><i class="fa fa-pencil"></i></a>';
                    }
                    if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($page->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $page->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin::pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Repositories\PagesRepository $pagesRepository
     * @param  PagesCreateRequest $request
     * @return Response
     */
    public function store(
    PageCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Modules\Admin\Models\Page
     * @return Response
     */
    public function edit(Page $pageDetails)
    {
        $response['success'] = true;
        $response['form'] = view('admin::pages.edit', compact('pageDetails'))->render();

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\PageUpdateRequest $request
     * @param  Modules\Admin\Models\Page
     * @return Response
     */
    public function update(
    PageUpdateRequest $request, $pages)
    {
        $response = $this->repository->update($request->all(), $pages);
        return response()->json($response);
    }
}
