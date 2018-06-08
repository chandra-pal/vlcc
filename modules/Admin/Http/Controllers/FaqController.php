<?php
/**
 * The class for managing FAQ specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Admin\Models\Faq;
use Modules\Admin\Repositories\FaqCategoryRepository;
use Modules\Admin\Repositories\FaqRepository;
use Modules\Admin\Http\Requests\FaqCreateRequest;
use Modules\Admin\Http\Requests\FaqUpdateRequest;
use Modules\Admin\Services\Helper\ConfigConstantHelper;

class FaqController extends Controller
{

    /**
     * The FaqRepository instance.
     *
     * @var Modules\Admin\Repositories\FaqRepository
     */
    protected $repository;

    /**
     * The FaqRepository instance.
     *
     * @var Modules\Admin\Repositories\FaqCategoryRepository
     */
    protected $faqCategoryRepository;

    /**
     * Create a new FaqController instance.
     *
     * @param  Modules\Admin\Repositories\FaqRepository $repository
     * @return void
     */
    public function __construct(FaqRepository $repository, FaqCategoryRepository $faqCategoryRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->faqCategoryRepository = $faqCategoryRepository;
    }

    /**
     * List all the data
     * @return response
     */
    public function index()
    {
        $allCategoriesList = $this->faqCategoryRepository->listAllCategoriesData()->toArray();
        $categoryList = $this->faqCategoryRepository->listCategoryData()->toArray();

        return view('admin::faq.index', compact('categoryList', 'allCategoriesList'));
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
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.deleted', ['name' => 'FAQ']);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.notDeleted', ['name' => 'FAQ']);
        }

        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $faqs = $this->repository->data();


        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $faqs = $faqs->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($faqs)
                ->addColumn('status', function ($faq) {
                    $status = ($faq->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                    return $status;
                })
                ->addColumn('question_answer', function ($faq) {
                    $actionList = '';
                    $actionList = '<b>' . $faq->question . '</b><br />[' . $faq->answer . ']';
                    return $actionList;
                })
                ->addColumn('created_at', function ($faq) {
                    $dateTimeFormat = ConfigConstantHelper::getValue('C_DATEFORMAT');
                    $created_at = date($dateTimeFormat, strtotime($faq->created_at));
                    return $created_at;
                })
                ->addColumn('action', function ($faq) {
                    $actionList = '';
                    if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($faq->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList = '<a href="javascript:;" id="' . $faq->id . '" data-action="edit" data-id="' . $faq->id . '" id="' . $faq->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                    }
                    if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($faq->created_by == Auth::guard('admin')->user()->id))) {
                        $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $faq->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                    }
                    return $actionList;
                })
                ->make(true);
    }

    /**
     * Display a form to create new faq.
     *
     * @return view as response
     */
    public function create()
    {
        $categoryList = $this->faqCategoryRepository->listCategoryData()->toArray();

        return view('admin::faq.create', compact('categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FaqCreateRequest $request
     * @return json encoded Response
     */
    public function store(FaqCreateRequest $request)
    {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified configuration setting.
     *
     * @param  Modules\Admin\Models\Faq $faq, Modules\Admin\Repositories\FaqCategoryRepository $faqCategoryRepository
     * @return json encoded Response
     */
    public function edit(Faq $faq)
    {
        $categoryList = $this->faqCategoryRepository->listCategoryData()->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::faq.edit', compact('faq', 'categoryList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FaqUpdateRequest $request, Modules\Admin\Models\Faq $faq
     * @return json encoded Response
     */
    public function update(FaqUpdateRequest $request, Faq $faq)
    {
        $response = $this->repository->update($request->all(), $faq);

        return response()->json($response);
    }
}
