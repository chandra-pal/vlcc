<?php
/**
 * The class for managing Member Escalation Matrix.
 * 
 * 
 * @author Priyanka Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\EscalationMatrix;
use Modules\Admin\Repositories\EscalationMatrixRepository;
use Modules\Admin\Repositories\CPRRepository;
use Illuminate\Http\Request;
use Session;

class EscalationMatrixController extends Controller
{

    /**
     * The OffersRepository instance.
     *
     * @var Modules\Admin\Repositories\OffersRepository
     */
    protected $repository;

    /**
     * Create a new OffersController instance.
     *
     * @param  Modules\Admin\Repositories\OffersRepository $repository
     * @return void
     */
    public function __construct(EscalationMatrixRepository $repository, CPRRepository $repository1)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->cpr_repository = $repository1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index()
    {
//        $inputs["member_id"] = 125;
//        $inputs["session_id"] = 21;
//        $inputs["package_id"] = 1;
//        $weightLoss=1.2;
//        $weightGain=0;
//        $this->cpr_repository->escalateMember($inputs, $weightLoss, $weightGain);
//        echo "index";
//        die;
        return view('admin::escalation-matrix.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData()
    {
        $params['user_type_id'] = Auth::guard('admin')->user()->user_type_id;
        $params['user_name'] = Auth::guard('admin')->user()->username;
        $escalation_matrix = $this->repository->data($params);
        //dd($escalation_matrix);

        
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $escalation_matrix = EscalationMatrix::filter(function ($row) {
                    return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
                });
        }
        return Datatables::of($escalation_matrix)
                ->addColumn('action', function ($escalation_matrix) {
                    $actionList = '';
                    $actionList .= '<a href="javascript:void(0)" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link escalate_member_click" id="cpr/' . $escalation_matrix->session_id . '">View History</i></a>';
                    return $actionList;
                })->addColumn('package_title', function ($escalation_matrix) {
                    $package_title = '';
                    $package_title = $escalation_matrix->Package->package_title;
                    return $package_title;
                })->addColumn('member', function ($escalation_matrix) {
                    $member_name = '';
                    $member_name = $escalation_matrix->Member->first_name . " - " . $escalation_matrix->Member->mobile_number;
                    return $member_name;
                })->addColumn('escalation_date', function ($escalation_matrix) {
                    $escalation_date = '';
                    $escalation_date = date('d-M-Y', strtotime($escalation_matrix->escalation_date));
                    return $escalation_date;
                })->addColumn('center', function ($escalation_matrix) {
                    $centerName = $this->repository->getMemberCenter($escalation_matrix->member_id);
                    return $centerName;
                })
                ->make(true);
    }

    /**
     * Display a form to create new offer 
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::escalation_matrix.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OffersCreateRequest $request
     * @return json encoded Response
     */
    public function store(OfferCreateRequest $request)
    {
        $offerTitle = ucfirst($request->all()["offer_title"]);
        $updateOffer = $this->repository->updateOffers($offerTitle);
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified Offer.
     *
     * @param  Modules\Admin\Models\Offer $Offer
     * @return json encoded Response
     */
    public function edit(Offer $escalation_matrix)
    {
        $response['success'] = true;
        $response['form'] = view('admin::escalation_matrix.edit', compact('escalation_matrix'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OffersCreateRequest $request, Modules\Admin\Models\Offer $escalation_matrix
     * @return json encoded Response
     */
    public function update(OfferUpdateRequest $request, Offer $escalation_matrix)
    {
        $response = $this->repository->update($request->all(), $escalation_matrix);
        return response()->json($response);
    }

    public function addAthComment(Request $request)
    {
        $params = $request->all();
        $result = $this->repository->addAthComment($params);
        $response['status'] = $result;
        return response()->json($response);
    }

    public function getAthComment(Request $request)
    {
        $params = $request->all();
        $result = $this->repository->getAthComment($params);
        if ($result != 0) {
            $response['ath_comment'] = $result["ath_comment"];
        } else {
            $response['status'] = $result;
        }
        return response()->json($response);
    }

    public function setMemberEscalationSession() {
        Session::set('escalation_history', 1);
        $response['status'] = 1;
        return response()->json($response);
    }
}
