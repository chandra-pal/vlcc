<?php
/**
 * The class for managing post specific actions.
 * 
 * 
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Models\Post;
use Modules\Admin\Repositories\PostRepository;
use Modules\Admin\Http\Requests\PostCreateRequest;
use Modules\Admin\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    /**
     * The PostRepository instance.
     *
     * @var Modules\Admin\Repositories\PostRepository
     */
    protected $repository;

    /**
     * Create a new PostController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(PostRepository $repository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\PostRepository $postRepository
     * @return response
     */

    public function index()
    {
        return view('admin::post.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
         $posts = $this->repository->data($request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $posts = $posts->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($posts)
                ->addColumn('status_format', function ($post) {
                    $status = ($post->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('action', function ($post) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($post->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $post->id . '" id="' . $post->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);

    }

    /**
     * Display a form to create new post.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\PostCreateRequest $request
     * @return json encoded Response
     */
    public function store(PostCreateRequest $request)
    {
        $response = $this->repository->store($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param  Modules\Admin\Models\Post $post
     * @return json encoded Response
     */
    public function edit(Post $post)
    {
        $response['success'] = true;
        $response['form'] = view('admin::post.edit', compact('post'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\PostUpdateRequest $request, Modules\Admin\Models\Post $post 
     * @return json encoded Response
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $response = $this->repository->update($request->all(), $post);

        return response()->json($response);
    }


}