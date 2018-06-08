<?php
/**
 * The class for managing activity type specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\SessionBookings;
use Modules\Admin\Repositories\SessionBookingsRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\SessionBookingsCreateRequest;
use Modules\Admin\Http\Requests\SessionBookingsUpdateRequest;
use Modules\Admin\Http\Requests\SessionBookingsDeleteRequest;
use Modules\Admin\Services\Helper\MemberHelper;
use Modules\Admin\Services\Helper\UserInfoHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use Session;
use Excel;

class SessionBookingsController extends Controller
{

    /**
     * The SessionBookingsRepository instance.
     *
     * @var Modules\Admin\Repositories\SessionBookingsRepository
     */
    protected $repository;

    /**
     * Create a new SessionBookingsController instance.
     *
     * @param  Modules\Admin\Repositories\SessionBookingsRepository $repository
     * @return void
     */
    public function __construct(SessionBookingsRepository $repository, CenterRepository $centerRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index()
    {
        $memberHelper = new MemberHelper();
        $membersList = [];

        if ((Auth::guard('admin')->user()->userType->id == 9) || (Auth::guard('admin')->user()->userType->id == 11)) {
            $centersList = $memberHelper->getCentersList();
            if (Session::get('center_id') != '') {
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
        } elseif (Auth::guard('admin')->user()->userType->id == 7 || Auth::guard('admin')->user()->userType->id == 8) {
            $centers = $memberHelper->getCentersList();
            $centerId = key($centers);
            if (isset($centerId) && $centerId != '') {
                $membersList = $this->centerRepository->getMembersList($centerId);
            }
        } else {
            $membersList = $memberHelper->getUserWiseMemberList();
        }
        $sessionStatus = array("2" => "Booked", "6" => "Waiting list");
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $timestamp = strtotime($arrTimes['end_time']) + 30 * 60;
        $arrTimes['end_time_session_calendar'] = date('H:i', $timestamp);
        $packageList = array();
        $serviceList = array();
        $dieticianId = Auth::guard('admin')->user()->id;
        $olaCabRequired = array("1" => "Yes", "0" => "No");
        $flag = "add-session";
        $cancelFlag = 0;
        $selectedSessionServices = null;
        $centerStaffList = array();
        $centerMachineList = array();
        $centerRoomList = array();
        $staffId = array();
        $machineId = array();
        $roomId = array();
        return view('admin::session-bookings.index', compact('arrTimes', 'membersList', 'packageList', 'serviceList', 'dieticianId', 'olaCabRequired', 'flag', 'cancelFlag', 'selectedSessionServices', 'membersList', 'centersList', 'centerStaffList', 'centerMachineList', 'centerRoomList', 'staffId', 'machineId', 'roomId','sessionStatus'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return event data
     */
    public function getData(Request $request)
    {
        $params['dietician_id'] = Auth::guard('admin')->user()->id;
        $params['logged_in_user_type_id'] = Auth::guard('admin')->user()->userType->id;
        $customer_id = $request->all()["customer_id"];
        $params["customer_id"] = $customer_id;
        Session::set('member_id', $customer_id);
        $sessionBookings = $this->repository->data($params);
        $events = [];
        foreach ($sessionBookings as $key => $session) {
            if ($session->status == 1) {
                $className = 'sessionRequested';
                $background_color = '#E87E04';
            } elseif ($session->status == 2) {
                $className = 'sessionBooked';
                $background_color = '#2AB4C0';
            } elseif ($session->status == 3) {
                $className = 'sessionRejected';
                $background_color = '#E43A45';
            } elseif ($session->status == 4) {
                $className = 'sessionCancelled';
                $background_color = '#525E64';
            } elseif ($session->status == 5) {
                $className = 'sessionCompleted';
                $background_color = '#3598DC';
            } elseif ($session->status == 6) {
                $className = 'sessionWaiting';
                $background_color = '#f4df41';
            }elseif ($session->status == 7) {
                $className = 'sessionConfirmed';
                $background_color = '#83f442';
            }elseif ($session->status == 8) {
                $className = 'sessionNoResponse';
                $background_color = '#f441c4';
            } else {
                $className = 'sessionNotAttended';
                $background_color = '#E6E6FA';
            }

            $displayText = ucwords($session->member->first_name . ' - ' . $session->member->mobile_number);
            $memberId = $session->member_id;
            $mobileNumber = $session->member->mobile_number;
            $finalDisplayText = $displayText;

            $events[] = [
                'id' => $session->id,
                'title' => $finalDisplayText,
                'allDay' => FALSE,
                'start' => $session->session_date . 'T' . $session->start_time,
                'end' => $session->session_date . 'T' . $session->end_time,
                'className' => $mobileNumber . " edit-form-link",
                'backgroundColor' => $background_color,
                'status' => $session->status,
                'mobile_number' => $session->mobile_number,
                'member_id' => $session->member_id,
                'created_by' => $session->created_by
            ];
        }

        return response()->json($events);
    }

    /**
     * Get a listing of the Todays Sessions.
     *
     * @return event data
     */
    public function getTodaysSessions()
    {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $params['user_type_id'] = Auth::guard('admin')->user()->user_type_id;
        $params['session_date'] = date('Y-m-d');
		$params['session_date1'] = date('Y-m-d',strtotime($params['session_date'].' -1 day'));
		$params['session_date2'] = date('Y-m-d',strtotime($params['session_date'].' -2 day'));
        
        $sessionBookings = $this->repository->data($params);
        $sessionBookings = collect($sessionBookings);

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $foods = $sessionBookings->filter(function ($row) {
                return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
            });
        }
        return Datatables::of($sessionBookings)
                ->addColumn('action', function ($sessionBookings) {
                    $actionList = '';
                    if (($sessionBookings->status == 7) && (!empty(Auth::guard('admin')->user()->hasAdd) && (Auth::guard('admin')->user()->userType->id != 11)) && $sessionBookings->package_id != 0) {
                        // if (($sessionBookings->status == 1 || $sessionBookings->status == 2) && !empty(Auth::guard('admin')->user()->hasAdd)) {
                        $actionList .= '<a href="cpr/' . $sessionBookings->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link">Start Session</i></a>';
                    }
                    if ($sessionBookings->package_id == 0 && $sessionBookings->status == 2) {
                        $actionList .= '<a href="javascript:void(0)" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link mark-session-complete" id=' . $sessionBookings->id . '>Mark as completed</i></a>';
                    }
                    return $actionList;
                })
                ->addColumn('session_date', function ($sessionBookings) {
                    $actionList = date('d-M-Y', strtotime($sessionBookings->session_date));
                    return $actionList;
                })
                ->addColumn('service_name', function ($sessionBookings) {
                    if ($sessionBookings->package_id != 0) {
                        $services = $sessionBookings->MemberPackage->services;
                        $services = $services->toArray();
                        $service_id = explode(",", $sessionBookings->service_id);
                        $string = '';
                        foreach ($services as $key => $value) {
                            if (in_array($services[$key]['id'], $service_id)) {
                                $string = $string . ", " . $services[$key]['service_name'];
                            }
                            $string = (!empty(ltrim($string, ",")) ? ltrim($string, ",") : 'NA');
                        }
                    } else {
                        $services_name = $this->repository->beautyServiceName($sessionBookings->service_id);
                        $string = $services_name;
                    }

                    return $string;
                })
                ->addColumn('start_time', function ($sessionBookings) {
                    $actionList = date('h:i a', strtotime($sessionBookings->start_time));
                    return $actionList;
                })
                ->addColumn('end_time', function ($sessionBookings) {
                    $actionList = date('h:i a', strtotime($sessionBookings->end_time));
                    return $actionList;
                })
                ->addColumn('first_name', function ($sessionBookings) {
                    $member_name = $sessionBookings->member->first_name . " - " . $sessionBookings->member->mobile_number;
                    return $member_name;
                })
                ->addColumn('status', function ($sessionBookings) {
                    $sessionStatus = array("1" => "Requested","2" => "Booked","3" => "Rejected", "4" => "Cancelled","5" => "Completed","6" =>"Waiting list","7" =>"Confirmed","8" =>"No Response");
                    $actionList = $sessionStatus[$sessionBookings->status];
                    return $actionList;
                })
                ->addColumn('center_name', function ($sessionBookings) {
                    $memberId = $sessionBookings->member_id;
                    $center_name = $this->repository->getMemberCenterName($memberId);
                    return $center_name;
                })
//                        }
                ->make(true);
    }

    public function show(SessionBookings $sessionBookings)
    {
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $memberHelper = new MemberHelper();
        $membersList = $memberHelper->getUserWiseMemberList();
        $params["member_id"] = $sessionBookings->member_id;
        Session::set('member_id', $sessionBookings->member_id);
        $params["package_id"] = $sessionBookings->package_id;
        $packageList = $this->repository->getMemberPackages($params)->toArray();

        // Get Services List of Selected Package
        $servicesList = $this->repository->getMemberPackageServices($params);
        $serviceList = $servicesList["remaining_services"]->toArray();

        // Get Selected Services List
        $selectedSessionServices = explode(",", $sessionBookings->service_id);

        $dieticianId = Auth::guard('admin')->user()->id;
        $olaCabRequired = array("1" => "Yes", "0" => "No");
        $sessionBookings->session_date = date('d M Y', strtotime($sessionBookings->session_date));

        $sessionBookings->start_time = date('h:i A', strtotime($sessionBookings->start_time));
        $sessionBookings->end_time = ($sessionBookings->end_time != '00:00:00') ? date('h:i A', strtotime($sessionBookings->end_time)) : '';

        if ($sessionBookings->status == 1) {
            $sessionStatus = array("1" => "Requested", "2" => "Confirm", "3" => "Reject");
        } else if ($sessionBookings->status == 2) {
            $sessionStatus = array("2" => "Booked", "4" => "Cancel");
        }
        $flag = "update-session";

        $cancelFlag = 0;

        $currentTime = date("Y-m-d H:i:s");
        $sessionDate = $sessionBookings->session_date . " " . $sessionBookings->start_time;
        $sessionDate = date("Y-m-d H:i:s", strtotime($sessionDate));


//        if ((($sessionBookings->status == 1 || $sessionBookings->status == 2) && ($currentTime > $sessionDate)) || ($sessionBookings->status == 3 || $sessionBookings->status == 4 || $sessionBookings->status == 5)) {
//            $cancelFlag = 1;
//            $sessionStatus = array("4" => "Cancelled");
//        }

        if ($sessionBookings->status == 3 || $sessionBookings->status == 4 || $sessionBookings->status == 5) {
            $cancelFlag = 1;
            if ($sessionBookings->status == 3) {
                $sessionStatus = array("3" => "Rejected");
            } else if ($sessionBookings->status == 4) {
                $sessionStatus = array("4" => "Cancelled");
            } else if ($sessionBookings->status == 5) {
                $sessionStatus = array("5" => "Completed");
            }
        }

        return view('admin::session-bookings.update-session', compact('sessionBookings', 'arrTimes', 'membersList', 'packageList', 'dieticianId', 'olaCabRequired', 'flag', 'sessionStatus', 'serviceList', 'selectedSessionServices', 'cancelFlag'));
    }

    public function viewTodaysSessions()
    {
        $flag = 'add-session';
        return view('admin::session-bookings.view-todays-sessions', compact('flag'));
    }

    public function checkSessionBooking(Request $request)
    {
        $sessionBookings = $this->repository->checkSessionAvailability($request->all());
        $count = $sessionBookings;
        return $count;
    }

    /**
     * Display a form to create new activity type.
     *
     * @return view as response
     */
    public function create()
    {
        $flag = 'add-session';
        return view('admin::session-bookings.create', compact('flag'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\SessionBookingsCreateRequest $request
     * @return json encoded Response
     */
    public function store(Request $request)
    {
        $requestParamas = $request->all();
        $requestParamas['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        //$rules['member_id'] = 'required';
        $rules['package_id'] = 'required';
        $rules['session_date'] = 'required';
        $rules['start_time'] = 'required';
        $rules['end_time'] = 'required';

        //$messages['member_id.required'] = 'Please Select Customer.';
        $messages['package_id.required'] = 'Please Select Package.';
        $messages['session_date.required'] = 'Please Select Session Date.';
        $messages['start_time.required'] = 'Please Select Start time.';
        $messages['end_time.required'] = 'Please Select End Time.';

        $validator = Validator::make($requestParamas, $rules, $messages);
        if ($validator->fails()) {
            $validation_message = $validator->errors()->first();
            $response['status'] = 'error';
            $response['message'] = $validation_message;
        } else {
            $packageFlag = $this->repository->checkPackageActive($requestParamas["package_id"]);
            if ($packageFlag) {
                $requestParamas["session_date"] = date('Y-m-d', strtotime($requestParamas["session_date"]));
                $requestParamas["start_time"] = date('H:i', strtotime($requestParamas['start_time']));
                $requestParamas["end_time"] = date('H:i', strtotime($requestParamas['end_time']));
                $response = $this->repository->create($requestParamas);
            } else {
                $response['status'] = 'error';
                $response['message'] = "Package you have selected is not valid.";
            }
        }
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified activity type.
     *
     * @param  Modules\Admin\Models\SessionBookings $sessionBookings
     * @return json encoded Response
     */
    public function edit(SessionBookings $sessionBookings)
    {
        $serviceList = array();

        $session_id = $sessionBookings['id'];
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $timestamp = strtotime($arrTimes['end_time']) + 60 * 60;
        $arrTimes['end_time_session_calendar'] = date('H:i', $timestamp);

        $memberHelper = new MemberHelper();
        $membersList = $memberHelper->getUserWiseMemberList();
        $params["member_id"] = $sessionBookings->member_id;
        $params["package_id"] = $sessionBookings->package_id;
        $packageList = $this->repository->getMemberPackages($params)->toArray();

        // Get Services List of Selected Package
        $servicesList = $this->repository->getMemberPackageServices($params);
        $serviceList = $servicesList["remaining_services"]->toArray();

        // Get Selected Services List
        $selectedSessionServices = explode(",", $sessionBookings->service_id);


        $dieticianId = Auth::guard('admin')->user()->id;
        $olaCabRequired = array("1" => "Yes", "0" => "No");
        $sessionBookings->session_date = date('d M Y', strtotime($sessionBookings->session_date));

        $sessionBookings->start_time = date('h:i A', strtotime($sessionBookings->start_time));
        $sessionBookings->end_time = ($sessionBookings->end_time != '00:00:00') ? date('h:i A', strtotime($sessionBookings->end_time)) : '';

        if ($sessionBookings->status == 1) {
            $sessionStatus = array("1" => "Requested", "2" => "Confirm", "3" => "Reject","6" =>"Waiting list");
        }/* else if ($sessionBookings->status == 2) {
            $sessionStatus = array("2" => "Booked", "4" => "Cancel","9" =>"Waiting list","10" =>"Confirmed","11" =>"No Response");
        }*/ else {
            $sessionStatus = array("2" => "Booked", "4" => "Cancel","6" =>"Waiting list","7" =>"Confirmed","8" =>"No Response");
        }
        $flag = "update-session";
        $cancelFlag = 0;

        $currentTime = date("Y-m-d H:i:s");
        $sessionDate = $sessionBookings->session_date . " " . $sessionBookings->start_time;
        $sessionDate = date("Y-m-d H:i:s", strtotime($sessionDate));


        /* if (($sessionBookings->status == 1 || $sessionBookings->status == 2) && $currentTime > $sessionDate) {
          $cancelFlag = 1;
          $sessionStatus = array("4" => "Cancelled");
          } */

        if ($sessionBookings->status == 3 || $sessionBookings->status == 4 || $sessionBookings->status == 5) {
            $cancelFlag = 1;
            if ($sessionBookings->status == 3) {
                $sessionStatus = array("3" => "Rejected");
            } else if ($sessionBookings->status == 4) {
                $sessionStatus = array("4" => "Cancelled");
            } else if ($sessionBookings->status == 5) {
                $sessionStatus = array("5" => "Completed");
            }
        }

        $list = $this->repository->getAvailabilityList($params);

        $sessionDetails = $this->repository->getSessionResourceDetails($session_id);

        if (!empty($list['staff_list']->toArray())) {
            foreach ($list['staff_list'] as $key => $value) {
                $centerStaffList[$value['id']] = $value['first_name'] . " " . $value['last_name'];
            }
        } else {
            $centerStaffList = array();
        }

        if (!empty($list['machine_list']->toArray())) {
            foreach ($list['machine_list'] as $key => $value) {
                $centerMachineList[$value['id']] = $value['name'];
            }
        } else {
            $centerMachineList = array();
        }


        if (!empty($list['room_list']->toArray())) {
            foreach ($list['room_list'] as $key => $value) {
                $centerRoomList[$value['id']] = $value['name'];
            }
        } else {
            $centerRoomList = array();
        }

        $staffId = array();
        $machineId = array();
        $roomId = array();
        if (!empty($sessionDetails->toArray())) {

            foreach ($sessionDetails->toArray() as $key => $value) {
                if ($value['resource_type'] == 1) {
                    array_push($staffId, $value['resource_id']);
                } else if ($value['resource_type'] == 2) {
                    array_push($machineId, $value['resource_id']);
                } else if ($value['resource_type'] == 3) {
                    array_push($roomId, $value['resource_id']);
                }
            }
        }

        $response['staff_list'] = View('admin::session-bookings.staff', compact('centerStaffList', 'staffId'))->render();
        $response['machine_list'] = View('admin::session-bookings.machine', compact('centerMachineList', 'machineId'))->render();
        $response['room_list'] = View('admin::session-bookings.room', compact('centerRoomList', 'roomId'))->render();

        $response['success'] = true;
        $response['form'] = view('admin::session-bookings.edit', compact('sessionBookings', 'arrTimes', 'membersList', 'packageList', 'dieticianId', 'olaCabRequired', 'flag', 'sessionStatus', 'cancelFlag', 'serviceList', 'selectedSessionServices', 'centerStaffList', 'centerMachineList', 'centerRoomList', 'staffId', 'machineId', 'roomId'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\SessionBookingsCreateRequest $request, Modules\Admin\Models\SessionBookings $sessionBookings
     * @return json encoded Response
     */
    public function update(Request $request, SessionBookings $sessionBookings)
    {
        $requestParamas = $request->all();
        $requestParamas['updated_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
        //$rules['member_id'] = 'required';
        $rules['package_id'] = 'required';
        $rules['session_date'] = 'required';
        $rules['start_time'] = 'required';
        $rules['end_time'] = 'required';

        //$messages['member_id.required'] = 'Please Select Customer.';
        $messages['package_id.required'] = 'Please Select Package.';
        $messages['session_date.required'] = 'Please Select Session Date.';
        $messages['start_time.required'] = 'Please Select Start time.';
        $messages['end_time.required'] = 'Please Select End Time.';

        $validator = Validator::make($requestParamas, $rules, $messages);
        if ($validator->fails()) {
            $validation_message = $validator->errors()->first();
            $response['status'] = 'error';
            $response['message'] = $validation_message;
        } else {
            $packageFlag = $this->repository->checkPackageActive($requestParamas["package_id"]);
            if ($packageFlag) {
                $requestParamas["session_date"] = date('Y-m-d', strtotime($requestParamas["session_date"]));
                $requestParamas["start_time"] = date('H:i', strtotime($requestParamas['start_time']));
                $requestParamas["end_time"] = date('H:i', strtotime($requestParamas['end_time']));

                $requestParamas['previous_session_date'] = date('Y-m-d', strtotime($requestParamas['previous_session_date']));
                $requestParamas['previous_start_time'] = date('H:i', strtotime($requestParamas['previous_start_time']));
                $requestParamas['previous_end_time'] = date('H:i', strtotime($requestParamas['previous_end_time']));
                $response = $this->repository->update($requestParamas, $sessionBookings);
            } else {
                $response['status'] = 'error';
                $response['message'] = "Package you have selected is not valid.";
            }
        }
        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\SessionBookingsDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(SessionBookingsDeleteRequest $request, SessionBookings $sessionBookings)
    {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/session-bookings.session-bookings')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/session-bookings.session-bookings')])];
        }

        return response()->json($response);
    }

    public function getPackagesList(Request $request)
    {
        $params['member_id'] = $request->all()["member_id"];
        $packageList = $this->repository->getMemberPackages($params)->toArray();
        $packageList = $packageList + array('0' => 'Others');
        $response['package_list'] = View('admin::session-bookings.packagedropdown', compact('packageList'))->render();
        return response()->json($response);
    }

    public function getServicesList(Request $request)
    {
        $params['member_id'] = filter_var($request->all()["member_id"], FILTER_VALIDATE_INT);
        $params['package_id'] = filter_var($request->all()["package_id"], FILTER_VALIDATE_INT);
        $used_services = [];
        if ($params['package_id'] == 0) {
            $serviceList = $this->repository->getBeautyServices($params)->toArray();
        } else {
            $servicesList = $this->repository->getMemberPackageServices($params);
            $serviceList = $servicesList["remaining_services"]->toArray();
            $used_services = $servicesList["used_services"]->toArray();
            //$unpaid_services = $servicesList["unpaid_services"]->toArray();
        }
        $selectedServices = [];
        $response['service_list'] = View('admin::session-bookings.servicedropdown', compact('serviceList', 'selectedServices', 'used_services'))->render();
        return response()->json($response);
    }

    public function getAvailabilityList(Request $request)
    {
        $params['member_id'] = $request->all()["member_id"];
        $list = $this->repository->getAvailabilityList($params);

        if (isset($list['staff_list']) && !empty($list['staff_list']->toArray())) {
            foreach ($list['staff_list'] as $key => $value) {
                $centerStaffList[$value['id']] = $value['first_name'] . " " . $value['last_name'];
            }
        } else {
            $centerStaffList = array();
        }

        if (isset($list['machine_list']) && !empty($list['machine_list']->toArray())) {
            foreach ($list['machine_list'] as $key => $value) {
                $centerMachineList[$value['id']] = $value['name'];
            }
        } else {
            $centerMachineList = array();
        }

        if (isset($list['room_list']) && !empty($list['room_list']->toArray())) {
            foreach ($list['room_list'] as $key => $value) {
                $centerRoomList[$value['id']] = $value['name'];
            }
        } else {
            $centerRoomList = array();
        }

        $staffId = "";
        $machineId = "";
        $roomId = "";

        $response['center_id'] = $list['center_id'];
        $response['staff_list'] = View('admin::session-bookings.staff', compact('centerStaffList', 'staffId'))->render();
        $response['machine_list'] = View('admin::session-bookings.machine', compact('centerMachineList', 'machineId'))->render();
        $response['room_list'] = View('admin::session-bookings.room', compact('centerRoomList', 'roomId'))->render();
        return response()->json($response);
    }

    public function fetchResources(Request $request)
    {
        $params['id'] = $request->all()['id'];
        $params['center_id'] = $request->all()['center_id'];
        $params['flag'] = $request->all()['flag'];

        $result = $this->repository->fetchResources($params);

        $resources = [];
        foreach ($result as $key => $value) {
            if ($params['flag'] == 1) {
                $title = $value['first_name'];
            } else {
                $title = $value['name'];
            }
            $resources[] = [
                'id' => $value['id'],
                'title' => $title,
                'eventColor' => ""
            ];
        }
        return response()->json($resources);
    }

    public function fetchAvailability(Request $request)
    {

        $params['id'] = $request->all()['id'];
        $params['center_id'] = $request->all()['center_id'];
        $params['date'] = $request->all()['date'];
        $params['flag'] = $request->all()['flag'];

        $list = $this->repository->fetchAvailability($params);
        $events = [];
        foreach ($list as $key => $value) {
            $param['start_time'] = $value['start_time'];
            $param['end_time'] = $value['end_time'];
            $param['id'] = $request->all()['id'];
            $param['availability_date'] = $value['availability_date'];

            if ($params['flag'] == 1) {
                $resource_id = $value['staff_id'];
            } else if ($params['flag'] == 2) {
                $resource_id = $value['machine_id'];
            } else if ($params['flag'] == 3) {
                $resource_id = $value['room_id'];
            }

            $events[] = [
                'id' => $value['id'],
                'resourceId' => $resource_id,
                'start' => $value['availability_date'] . 'T' . date('H:i:s', strtotime($param['start_time'])),
                'end' => $value['availability_date'] . 'T' . date('H:i:s', strtotime($param['end_time'])),
                'title' => ''
            ];

            $sessionList = $this->repository->fetchSessionList($param, $params['flag']);

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
            }
        }

        return response()->json($events);
    }

    public function updateSessionStatus(Request $request)
    {
        $session_id = filter_var($request->all()["session_id"], FILTER_SANITIZE_NUMBER_INT);
        $result = $this->repository->updateSessionStatus($session_id);
        $response["flag"] = "success";
        return response()->json($response);
    }

    public function bookingHistory(Request $request)
	{
	    $params['dietician_id'] = Auth::guard('admin')->user()->id;
		$params['from'] = $request->all()["from"];
		$params['from'] = date('Y-m-d', strtotime($params['from']));
		$params['to'] = $request->all()["to"];
		$params['to'] = date('Y-m-d', strtotime($params['to']));
		$flag = 1;
		$var = $this->repository->bookingHistoryStatus($params,$flag);

	    return view('admin::session-bookings.booking-history',compact('var','params'));
    }
	
	public function downloadBookingHistory(Request $request)
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];
			$flag = 0;
            $params['dietician_id'] = Auth::guard('admin')->user()->id;
			$params['from'] = $request->all()["from"];
			$params['to'] = $request->all()["to"];
			$data = $this->repository->bookingHistoryStatus($params,$flag);
			
			//print_r($data);
			//exit;
            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Previous-Booking-History-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Previous Booking History', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;
							if($tempItem['package_id']==0)
							{
								$tempItem['package_title']='Others';
								$tempItem['service_name']=$tempItem['service_name1'];
							}
							if($tempItem['status']==1)
								$tempItem['status']='Requested';
							elseif($tempItem['status']==2)
								$tempItem['status']='Booked';
							elseif($tempItem['status']==3)
								$tempItem['status']='Rejected(by dietician)';
							elseif($tempItem['status']==4)
								$tempItem['status']='Canceled(by dietitian)';
							elseif($tempItem['status']==5)
								$tempItem['status']='Canceled(by customer)';
							elseif($tempItem['status']==6)
								$tempItem['status']='Waiting List';
							elseif($tempItem['status']==7)
								$tempItem['status']='Confirmed';
							else
								$tempItem['status']='No Response';
                            $requireExportData = [
//                                'index' => '',
                                'customer' => $tempItem['first_name'],
                                'mobile Number' => $tempItem['mobile_number'],
                                'Package' => $tempItem['package_title'],
                                'Service' => $tempItem['service_name'],
                                'Appointment Date' => $tempItem['session_date'],
                                'Start Time' => $tempItem['start_time'],
                                'End Time' => $tempItem['end_time'],
                                'Status' => $tempItem['status'],
								'Created By' => $tempItem['Created_BY'],
								'Updated By' => $tempItem['Updated_BY']
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('Customer', 'Mobile Number', 'Package', 'Service', 'Appointment Date', 'Start Time', 'End Time', 'Status', 'Created By', 'Updated By'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Detailed Sales Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('session-bookings/booking-history')->with('msg-error', 'Error to export data.');
        }
        redirect('session-bookings/booking-history')->with('msg-success', 'Data Export successfuly!!');
    }
}
