<?php

/**
 * The class for managing member diet deviation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\Deviation;
use Modules\Admin\Repositories\DeviationRepository;
use Illuminate\Http\Request;

class DeviationController extends Controller {

    /**
     * The DeviationRepository instance.
     *
     * @var Modules\Admin\Repositories\DeviationRepository
     */
    protected $repository;

    /**
     * Create a new DeviationController instance.
     *
     * @param  Modules\Admin\Repositories\DeviationRepository $repository
     * @return void
     */
    public function __construct(DeviationRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    public function index(Request $request) {
        return view('admin::deviation.list');
    }

    public function getListData(Request $request) {
        $params['date'] = date('Y-m-d');
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $deviation = $this->repository->dataList($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $deviation = $deviation->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($deviation)
                        ->addColumn('action', function ($deviation) {
                            $actionList = '';
                                $date = str_replace("-", "", $deviation['deviation_date']);
                                $parameter = $date . "-" . $deviation['member_id'];
                                $actionList = '<a href="' . route('admin.member-diet-log.index', ['mid' => $parameter]) . '" data-action="view" data-id="' . $deviation['devitionId'] . '"  id="' . $deviation['devitionId'] . '" class="btn btn-xs default margin-bottom-5 blue view-link" title="Edit">VIEW</a>';
                                return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function deviations(Request $request, $dateScheduleTypeId) {
        $slug = explode("-", $dateScheduleTypeId);
        $date = $slug[0];
        $date = strtotime($date);
        $newDate = date('Y-m-d', $date);
        $scheduleType = $slug[1];
//        $scheduleType = 1;
        $diteticianId = Auth::guard('admin')->user()->username;
        return view('admin::deviation.index', compact('newDate', 'scheduleType', 'diteticianId'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['date'] = $request->input('date');
        $params['schedule_type'] = $request->input('schedule_type');
        $params['diteticianId'] = $request->input('diteticianId');

        $deviation = $this->repository->data($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $deviation = $deviation->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($deviation)
                        ->addColumn('action', function ($deviation) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($deviation->created_by == Auth::guard('admin')->user()->id))) {
                                $date = str_replace("-", "", $deviation['deviation_date']);
                                $parameter = $date . "-" . $deviation['member_id'];
                                $actionList = '<a href="' . route('admin.member-diet-log.index', ['mid' => $parameter]) . '" data-action="view" data-id="' . $deviation['devitionId'] . '"  id="' . $deviation['devitionId'] . '" class="btn btn-xs default margin-bottom-5 blue view-link" title="Edit">VIEW</a>';
                                return $actionList;
                            }
                        })
                        ->make(true);
    }

}
