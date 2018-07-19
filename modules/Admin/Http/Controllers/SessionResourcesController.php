<?php

/**
 * The class for managing food specific actions.
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
use Mockery\Exception;
use Modules\Admin\Repositories\SessionResourcesRepository;
use Modules\Admin\Services\Helper\MemberHelper;
use Illuminate\Http\Request;
use Excel;
use Session;
use Carbon\Carbon;

class SessionResourcesController extends Controller {

    /**
     * The FoodRepository instance.
     *
     * @var Modules\Admin\Repositories\FoodRepository
     */
    protected $repository;

    /**
     * Create a new FoodController instance.
     *
     * @param  Modules\Admin\Repositories\FoodRepository $repository
     * @return void
     */
    public function __construct(SessionResourcesRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $date = \Carbon\Carbon::now()->format('d-m-Y');
        $memberHelper = new MemberHelper();
        $created_by_user_type = Auth::guard('admin')->user()->userType->id;
        $arrTimes['start_time'] = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = config('settings.APP_SESSION_BOOKING_END_TIME');
        $timestamp = strtotime($arrTimes['end_time']) + 60 * 30;
        $arrTimes['end_time_resource_calendar'] = date('H:i', $timestamp);
        if ($created_by_user_type == 4 || $created_by_user_type == 7 || $created_by_user_type == 8 || $created_by_user_type == 9 || $created_by_user_type == 11) {//7:CH, 8:SH, 9:ATH, 11:CA
            // Fetch Centers list
            $centersList = $memberHelper->getCentersList();
        } else if ($created_by_user_type == 7) {
            // Get Center Name
        } else {
            // 404 Not found
        }
        
        return view('admin::session-resources.index', compact('centersList', 'date', 'arrTimes'));
    }

    public function fetchResources(Request $request) {
        $params = $request->all();

        //to fetch center name for logged in center head
        $loggedInUserType = Auth::guard('admin')->user()->userType->id;
        if($loggedInUserType == 7){

            $loggedInUserId = Auth::guard('admin')->user()->id;
            $centerData = $this->repository->fetchCenterHeadCenter($loggedInUserId);
            $params['center_id'] = $centerData[0]->id;
        }

        $result = $this->repository->fetchResources($params);
        $resources = [];
        foreach ($result as $key => $value) {
            $resources[] = [
                'id' => $value['id'],
                'title' => isset($value['name']) ? $value['name'] : $value['first_name'] . ' ' . $value['last_name'],
                'eventColor' => "",
            ];
        }
        return response()->json($resources);
    }

    public function getResourcesAvailability(Request $request) {
        $params = $request->all();
        $flag = $request->all()['flag'];

        //to fetch center name for logged in center head
        $loggedInUserType = Auth::guard('admin')->user()->userType->id;
        if($loggedInUserType == 7){

            $loggedInUserId = Auth::guard('admin')->user()->id;
            $centerData = $this->repository->fetchCenterHeadCenter($loggedInUserId);
            $params['center_id'] = $centerData[0]->id;
        }

        $result = $this->repository->getResourcesAvailability($params);
        $events = [];
        foreach ($result as $key => $value) {
            $param['start_time'] = $value['start_time'];
            $param['end_time'] = $value['end_time'];
            $param['availability_date'] = $value['availability_date'];

            if ($flag == 1) {
                $resource_id = $value['staff_id'];
            } else if ($flag == 2) {
                $resource_id = $value['machine_id'];
            } else if ($flag == 3) {
                $resource_id = $value['room_id'];
            }
            $events[] = [
                'id' => $value['id'],
                'resourceId' => $resource_id,
                'start' => $value['availability_date'] . "T" . $value['start_time'],
                'end' => $value['availability_date'] . "T" . $value['end_time'],
                'title' => "",
            ];


            
            /*$sessionList = $this->repository->fetchSessionList($param, $params['flag']);
            
            foreach ($sessionList->toArray() as $s_key => $s_value) {
                if ($resource_id == $s_value['resource_id']) {
                    $events[] = [
                        'id' => $s_value['id'],
                        'resourceId' => $s_value['resource_id'],
                        'start' => $value['availability_date'] . 'T' . date('H:i:s', strtotime($s_value['start_time'])),
                        'end' => $value['availability_date'] . 'T' . date('H:i:s', strtotime($s_value['end_time'])),
                        'title' => $s_value['first_name'],
                        'backgroundColor' => 'yellow',
                    ];
                }
            }*/
        }
        $result = $this->repository->getBookedResources($params);

        foreach ($result as $key => $value) {
            if ($flag == 1) {
                $resource_id = $value['user_id'];
            } else if ($flag == 2) {
                $resource_id = $value['machine_id'];
            } else if ($flag == 3) {
                $resource_id = $value['id'];
            }
            $events[] = [
                'id' => $value['id'],
                'resourceId' => $resource_id,
                'start' => $value['session_date'] . "T" . $value['start_time'],
                'end' => $value['session_date'] . "T" . $value['end_time'],
                'backgroundColor'=>"yellow",
                'title' => $value['first_name']."-".$value['mobile_number'],
            ];
        }
      
        return response()->json($events);
    }

    public function setDownloadData(Request $request){
        $params = $request->all();
        Session::forget('center_id');
        Session::forget('flag');
        Session::forget('from_date');
        Session::forget('to_date');
        if(strtotime($params['from_date']) > strtotime($params['to_date'])){
            $responce = array('msgCode'=>'0','msg'=>'error:Invalid Date Range');

        } else {
            Session::set('center_id', $params['center_id']);
            Session::set('flag', $params['flag']);
            Session::set('from_date', $params['from_date']);
            Session::set('to_date', $params['to_date']);
            $responce = array('msgCode'=>'200','msg'=>'success');
        }

        return response()->json($responce);
    }

    public function downloadExcel(){

        $params['center_id'] = Session::get('center_id');
        $params['flag'] = Session::get('flag');
        $params['from_date'] = Session::get('from_date');
        $params['to_date'] = Session::get('to_date');

        try{

            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];

            $data = $result = $this->repository->dateWiseBookedResources($params);

            if ($params['flag'] == 1){
                $fileTitle1 = 'Datewise-Staff-';
                $fileTitle2 = 'Datewise_Staff_';

           } elseif ($params['flag'] == 2){

                $fileTitle1 = 'Datewise-Machine-';
                $fileTitle2 = 'Datewise_Machine_';

            } else {
                $fileTitle1 = 'Datewise-Room-';
                $fileTitle2 = 'Datewise_Room_';
            }

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = $fileTitle1.$uniqueTimeStr;


            Excel::create($fileName, function($excel) use ($data,$fileTitle2) {
                $excel->sheet($fileTitle2, function($sheet) use($data) {
                    if(Session::get('flag') == 1){
                        $title = 'Staff Name';
                    } elseif (Session::get('flag') == 2){
                        $title = 'Machine Name';

                    } else {
                        $title = 'Room Name';
                    }

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;
                            if($tempItem['package_id'] == 0){
                                $packageTitle = 'Other';
                                $serviceName = $tempItem['beauty_service_name'];
                            } else{
                                $packageTitle = $tempItem['package_title'];
                                $serviceName = $tempItem['service_name'];
                            }

                            if($tempItem['status'] == 2){
                                $status = 'Booked';
                            } elseif ($tempItem['status'] == 5){
                                $status = 'Completed';
                            } else{
                                $status = 'Confirmed';
                            }

                            if(Session::get('flag') == 1){
                                $titleName = $tempItem['staff_name'];
                            } elseif (Session::get('flag') == 2){
                                $titleName = $tempItem['machine_name'];

                            } else {
                                $titleName = $tempItem['name'];
                            }


                            $requireExportData = [
//                                'index' => '',
                                'date' => $tempItem['session_date'],
                                'time' => $tempItem['start_time'].'-'.$tempItem['end_time'],
                                'title' => $titleName,
                                'member_name' => ucwords($tempItem['first_name']),
                                'mobile_no' => $tempItem['mobile_number'],
                                'package' => $packageTitle,
                                'service' => $serviceName,
                                'status' => $status,
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('Date', 'Time', $title, 'Member Name', 'Mobile No', 'Package','Service','Status'));
                });
            })->export('xls');

        } catch (Exception $e){
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Session Resource Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

        }

    }

}
