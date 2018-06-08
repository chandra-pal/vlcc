<?php
/**
 * The class for managing product specific actions.
 *
 *
 * @author GAuri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\Report;
use Modules\Admin\Repositories\ReportsRepository;
use Modules\Admin\Repositories\MembersRepository;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use Modules\Admin\Services\Helper\UserInfoHelper;
use Modules\Admin\Services\Helper\ConfigConstantHelper;

class ReportsController extends Controller
{

    /**
     * The ProductsRepository instance.
     *
     * @var Modules\Admin\Repositories\ProductsRepository
     */
    protected $repository;
    protected $membersRepository;

    /**
     * Create a new ProductsController instance.
     *
     * @param  Modules\Admin\Repositories\ProductsRepository $repository
     * @return void
     */
    public function __construct(ReportsRepository $reportsRepository, MembersRepository $membersRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $reportsRepository;
        $this->membersRepository = $membersRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index()
    {

        return view('admin::reports.index');
    }

    public function centerWiseUsers()
    {
        $cityList = $this->repository->getCityList()->toArray();
        $centerList = [];
        return view('admin::reports.index', compact('centerList', 'cityList'));
        // return view('admin::reports.index');
    }

    public function getData()
    {
        $data = $this->repository->data();

        return Datatables::of($data)
                ->addColumn('first_name', function ($data) {
                    return ucwords($data->first_name . ' ' . $data->last_name);
                })
                ->make(true);
        $cityList = $this->repository->getCityList()->toArray();
        $centerList = [];
        return view('admin::reports.index', compact('centerList', 'cityList'));
        // return view('admin::reports.index');
    }

    public function viewCenterwiseUsers(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');
        Session::forget('city_id');
        Session::forget('center_id');
        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);
        $data = $this->repository->data($params);
        return Datatables::of($data)->addColumn('action', function ($data) {
                    
                })->make(true);
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function downloadExcel()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];

            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');
            $data = $this->repository->data($params);

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Centerwise-Users-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Centerwise Users list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;

                            $requireExportData = [
//                                'index' => '',
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'employee_id' => $tempItem['username'],
                                'first_name' => ucwords($tempItem['fullname']),
                                'mobile_no' => $tempItem['contact'],
                                'designation' => ucwords($tempItem['designation']),
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'Username', 'Name', 'Mobile No', 'Designation'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Detailed Sales Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports')->with('msg-error', 'Error to export data.');
        }
        redirect('reports')->with('msg-success', 'Data Export successfuly!!');
    }

    public function centerwiseLoggedUsers()
    {
        $cityList = $this->repository->getCityList()->toArray();
        $cityList = array('0' => 'All') + $cityList;
        $centerList = [];
        return view('admin::reports.userwise-login', compact('centerList', 'cityList'));
        //return view('admin::reports.userwise-login');
    }

    //Function for downloading Centerwise Logged In Users
    public function downloadCenterwiseLoggedUsers()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];

            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');
            $data = $this->repository->getCenterwiseLogin($params);

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Centerwise-LoggedIn-Users-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Centerwise LoggedIn Users list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;

                            $requireExportData = [
//                                'index' => '',
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'username' => $tempItem['username'],
                                'first_name' => ucwords($tempItem['fullname']),
                                'mobile_no' => $tempItem['contact'],
                                'designation' => ucwords($tempItem['designation']),
                                'login_count' => $tempItem['login_count'],
                                'last_login_datetime' => $tempItem['last_login_datetime']
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'Username', 'Name', 'Mobile No', 'Designation', 'Number of Logins', 'Last Login'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Detailed Sales Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/centerwise-logged-users')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/centerwise-logged-users')->with('msg-success', 'Data Export successfuly!!');
    }

    public function viewCenterwiseLoggedUsers(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');
        Session::forget('city_id');
        Session::forget('center_id');
        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);
        $data = $this->repository->getCenterwiseLogin($params);
        return Datatables::of($data)
                ->addColumn('last_login_datetime', function ($data) {
                    $dateTimeFormat = ConfigConstantHelper::getValue('C_DATEFORMAT');
                    $last_login_datetime = date($dateTimeFormat, strtotime($data->last_login_datetime));
                    return $last_login_datetime;
                })
                ->make(true);
    }

    public function centerwiseCustomers()
    {
        $cityList = $this->repository->getCityList()->toArray();

        //$data = $this->repository->getCenterList();
        //$centerList = $data->toArray();
        $centerList = [];
        return view('admin::reports.customer', compact('centerList', 'cityList'));
    }

    public function categorywiseCustomers()
    {
        $logged_in_by_user_type = Auth::guard('admin')->user()->userType->id;
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $city_id = 0;
        $center_id = 0;
        $cityList = $this->repository->getCityList()->toArray();
        $customerCategory = array("1" => "Successful Regular", "2" => "Successful Irregular", "3" => "Un-Successful Regular", "4" => "Un-Successful Irregular");
        $centerList = [];
        $userInfoHelper = new UserInfoHelper();
        $logged_in_user_center = $userInfoHelper->getLoggedInUserCenter($logged_in_user_id);
        if ($logged_in_by_user_type == 4 || $logged_in_by_user_type == 8 || $logged_in_by_user_type == 5 || $logged_in_by_user_type == 7) {
            $city_id = isset($logged_in_user_center[0]["city_id"]) ? $logged_in_user_center[0]["city_id"] : 0;
            $centerList = $this->repository->getCityWiseCenters($city_id)->toArray();
            $center_id = isset($logged_in_user_center[0]["center_id"]) ? $logged_in_user_center[0]["center_id"] : 0;
            Session::set('city_id', $city_id);
            Session::set('center_id', $center_id);
        } else if ($logged_in_by_user_type == 9) {
            // For ATH, retrieve multiple centers of ATH
            $cityList = [];
            foreach ($logged_in_user_center as $key) {
                $index = $key["city_id"];
                $value = $key["name"];
                $cityList[$index] = $value;
            }
        }
        return view('admin::reports.categorywise-customer', compact('centerList', 'cityList', 'logged_in_by_user_type', 'logged_in_user_id', 'city_id', 'center_id', 'customerCategory'));
    }

    public function getCenterListByCityName(Request $request)
    {
        $cityId = filter_var($request->all()["city_id"], FILTER_VALIDATE_INT);
        Session::set('city_id', $cityId);
        $centersList = [];
        if ($cityId > 0) {
            $centersList = $this->repository->getCityWiseCenters($cityId)->toArray();
            $centersList = array('0' => 'All') + $centersList;
        } elseif ($cityId == 0) {
            if ($request->all()["report_type"] == "userwise-cpr-count" || $request->all()["report_type"] == "centerwise-logged-users") {
                $centersList = array('0' => 'All') + $centersList;
            }
        }
        $response['centers_list'] = View('admin::reports.centerslist', compact('centersList'))->render();
        return response()->json($response);
    }

    public function viewCenterwiseCustomers(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');
        Session::forget('city_id');
        Session::forget('center_id');
        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);
        $data = $this->repository->getCenterwiseCustomers($params);
        return Datatables::of($data)
                ->addColumn('customer_name', function ($data) {
                    return ucwords($data->customer_name);
                })
                ->make(true);
    }

    public function viewCategorywiseCustomers(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');
        Session::forget('city_id');
        Session::forget('center_id');
        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);
        $data = $this->repository->getCenterwiseCustomers($params)->get();
        $data = collect($data);

        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $data = $data->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($data)
                ->addColumn('customer_name', function ($data) {
                    return ucwords($data->customer_name);
                })
                ->addColumn('category', function ($data) {
                    $category_flag = "";
                    $isSuccessful = $this->membersRepository->checkSuccessfulMember($data->member_id);
                    $isRegular = $this->membersRepository->checkRegularMember($data->member_id);
                    $category_flag = ($isSuccessful) ? 'Successful' : 'Unsuccessful';
                    $category_flag = ($isRegular) ? $category_flag . '- Regular' : $category_flag . ' - Irregular';
                    return $category_flag;
                })
                ->addColumn('action', function ($data) {
                    $actionList = '';
//                    $actionList = '<a href="javascript:;" data-action="view" data-id="' . $member['id'] . '"  id="' . $member['id'] . '" class="btn btn-xs default margin-bottom-5 blue view-link" title="Edit">VIEW</a>';
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('customer_name')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->customer_name), strtolower($request->get('customer_name'))) ? true : false;
                        });
                    }
                    if ($request->has('mobile_number')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->mobile_number), strtolower($request->get('mobile_number'))) ? true : false;
                        });
                    }
                    if ($request->has('customer_category')) {
                        $found = false;
                        $instance->collection = $instance->collection->filter(function ($row) use ($request, $found) {
                            $category_number = 0;
                            $category_flag = "";
                            $isSuccessful = $this->membersRepository->checkSuccessfulMember($row->member_id);
                            $isRegular = $this->membersRepository->checkRegularMember($row->member_id);
                            $category_flag = ($isSuccessful) ? 'Successful' : 'Unsuccessful';
                            $category_flag = ($isRegular) ? $category_flag . ' - Regular' : $category_flag . ' - Irregular';
                            if ($category_flag == "Successful - Regular") {
                                $category_number = 1;
                            } else if ($category_flag == "Successful - Irregular") {
                                $category_number = 2;
                            } else if ($category_flag == "Unsuccessful- Regular") {
                                $category_number = 3;
                            } else {
                                $category_number = 4;
                            }
                            if ($category_number == $request->get('customer_category')) {
                                $found = true;
                            }
                            return $found;
                        });
                    }
                })
                ->make(true);
    }

    // Function to download centerwise customers
    public function downloadCenterwiseCustomers()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];
            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');
            $data = $this->repository->getCenterwiseCustomers($params);

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Centerwise-Customers-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Centerwise Customers list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;

                            $requireExportData = [
//                                'index' => '',
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'name' => ucwords($tempItem['dietician_name']),
                                'username' => $tempItem['dietician_username'],
                                'designation' => $tempItem['designation'],
                                'customer_name' => ucwords($tempItem['customer_name']),
                                'mobile_number' => $tempItem['mobile_number'],
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'Name', 'Username', 'Designation', 'Customer Name', 'Mobile No'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Centerwise Customers Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/centerwise-customers')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/centerwise-customers')->with('msg-success', 'Data Exported successfuly!!');
    }

    // Function to download categorywise customers
    public function downloadCategorywiseCustomers()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];
            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');
            $data = $this->repository->getCenterwiseCustomers($params);
            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Categorywise-Customer-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Categorywise Customer list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;

                            $category_flag = "";
                            $isSuccessful = $this->membersRepository->checkSuccessfulMember($tempItem["member_id"]);
                            $isRegular = $this->membersRepository->checkRegularMember($tempItem["member_id"]);
                            $category_flag = ($isSuccessful) ? 'Successful' : 'Unsuccessful';
                            $category_flag = ($isRegular) ? $category_flag . '- Regular' : $category_flag . ' - Irregular';

                            $requireExportData = [
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'customer_name' => ucwords($tempItem['customer_name']),
                                'mobile_number' => $tempItem['mobile_number'],
                                'category' => $category_flag,
                                //'crm_center_id' => $tempItem['crm_center_id'],
                                'dietician_username' => $tempItem['dietician_username'],
                                'dietician_name' => $tempItem['dietician_name'],
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
                    // Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'Customer Name', 'Mobile Number', 'Category', 'Dietician Username', 'Dietician Name'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Categorywise Customers report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/categorywise-customers')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/categorywise-customers')->with('msg-success', 'Data Exported successfuly!!');
    }

    public function newUsers()
    {
        return view('admin::reports.new-users');
    }

    public function getNewUsers(Request $request)
    {
        $date = $request->all()['date'];

        $params['date'] = date("Y-m-d", strtotime($date));
        Session::set('new_user_date', $params['date']);

        $data = $this->repository->getNewUsers($params)->get();
        $data = collect($data);
        return Datatables::of($data)
                ->addColumn('first_name', function ($data) {
                    $last_name = $data->last_name != '' ? ucwords($data->last_name) : '';
                    return ucwords($data->first_name) . ' ' . $last_name;
                })
                ->addColumn('created_at', function ($data) {
                    return date("d-m-Y", strtotime($data->created_at));
                })
                ->make(true);
    }

    public function downloadNewUsers()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];
            $params['date'] = Session::get('new_user_date');
            $data = $this->repository->getNewUsers($params);
            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "First-Time-Users-Report-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {

                $excel->sheet('First Time User list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;

                            $requreExportData = [
                                'first_name' => $tempItem['first_name'],
                                'mobile_number' => $tempItem['mobile_number'],
                                'created_at' => $tempItem['created_at'],
                            ];
                            return $requreExportData;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('Customer Name', 'Mobile Number', 'Created At'));
                });
            })->export('xls');
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Categorywise Customers report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/categorywise-customers')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/categorywise-customers')->with('msg-success', 'Data Exported successfuly!!');
    }

    public function userwiseCprCount()
    {
        $cityList = $this->repository->getCityList()->toArray();
        $cityList = array('0' => 'All') + $cityList;
        $centerList = [];
        return view('admin::reports.userwise-cpr-count', compact('centerList', 'cityList'));
    }

    public function viewUserwiseCPRCount(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');
        Session::forget('city_id');
        Session::forget('center_id');
        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);
        $data = $this->repository->getUserwiseCPRCount($params);
        return Datatables::of($data)
                ->addColumn('percentage', function ($data) {
                    $percentage = 0;
                    if ($data->customer_count > 0) {
                        $percentage = ($data->cpr_usage_count / $data->customer_count) * 100;
                    }
                    return round($percentage);
                })
                ->addColumn('full_name', function ($data) {
                    $name = $data->full_name . " (" . $data->username . ")";
                    return $name;
                })
                ->make(true);
    }

    public function downloadCPRCount()
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];
            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');
            $data = $this->repository->getUserwiseCPRCount($params);

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Userwise-CPR-Count-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Userwise-CPR-Count', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {
                            $tempItem = (array) $item;
                            $cpr_usage_count = ($tempItem['cpr_usage_count'] != 0) ? $tempItem['cpr_usage_count'] : "0";
                            $customer_count = ($tempItem['customer_count'] != 0) ? $tempItem['customer_count'] : "0";
                            $percentage = ($tempItem['customer_count'] > 0) ? (($tempItem['cpr_usage_count'] / $tempItem['customer_count']) * 100) : 0;
                            $percentage = ($percentage != 0) ? round($percentage) : "0";
                            $requireExportData = [
                                'city_name' => $tempItem['city_name'],
                                'center_name' => $tempItem['center_name'],
                                'full_name' => $tempItem['full_name'],
                                'username' => $tempItem['username'],
                                'designation' => $tempItem['designation'],
                                'customer_count' => $customer_count,
                                'cpr_usage_count' => $cpr_usage_count,
                                'percentage' => $percentage,
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
                    // Add before first row
                    $sheet->prependRow(1, array('CIty', 'Center', 'Full Name', 'Username', 'Designation', 'Customer count', 'CPR Usage Count', 'Percentage'));
                });
            })->export('xls');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Userwise CPR Usage Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/userwise-cpr-count')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/userwise-cpr-count')->with('msg-success', 'Data Exported successfuly!!');
    }

    public function centerwiseEscalation()
    {
        $cityList = $this->repository->getCityList()->toArray();
        $centerList = [];
        return view('admin::reports.centerwise-escalation', compact('centerList', 'cityList'));
    }

    public function viewCenterwiseEscalation(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');

        Session::forget('city_id');
        Session::forget('center_id');

        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);

        $params['ATHfullname'] = $request->input('ATHfullname');
        $params['Dieticianfullname'] = $request->input('Dieticianfullname');
        $params['Memberfullname'] = $request->input('Memberfullname');
        $params['mobile_number'] = $request->input('mobile_number');

        Session::forget('ATHfullname');
        Session::forget('Dieticianfullname');
        Session::forget('Memberfullname');
        Session::forget('mobile_number');

        if (isset($params['ATHfullname']) && !empty($params['ATHfullname'])) {
            Session::set('ATHfullname', $params['ATHfullname']);
        }

        if (isset($params['Dieticianfullname']) && !empty($params['Dieticianfullname'])) {
            Session::set('Dieticianfullname', $params['Dieticianfullname']);
        }

        if (isset($params['Memberfullname']) && !empty($params['Memberfullname'])) {
            Session::set('Memberfullname', $params['Memberfullname']);
        }

        if (isset($params['mobile_number']) && !empty($params['mobile_number'])) {
            Session::set('mobile_number', $params['mobile_number']);
        }


        $data = $this->repository->getCenterwiseEscalation($params)->get();
        $data = collect($data);

        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $data = $data->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($data)
                ->addColumn('EDate', function ($data) {
                    $dateTimeFormat = ConfigConstantHelper::getValue('C_DATEFORMAT');
                    $EDate = date($dateTimeFormat, strtotime($data->EDate));
                    return $EDate;
                })
                ->addColumn('action', function ($data) {
                    $actionList = '';
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('ATHfullname')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(($row->ATHfullname), $request->get('ATHfullname')) ? true : false;
                        });
                    }
                    if ($request->has('Dieticianfullname')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->Dieticianfullname), strtolower($request->get('Dieticianfullname'))) ? true : false;
                        });
                    }
                    if ($request->has('Memberfullname')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->Memberfullname), strtolower($request->get('Memberfullname'))) ? true : false;
                        });
                    }
                    if ($request->has('mobile_number')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            //       return str_contains(strtolower($row->mobile_number), strtolower($request->get('mobile_number'))) ? true : false;
                            return str_contains(($row->mobile_number), $request->get('mobile_number')) ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    //// Function to download centerwise escalation
    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function downloadCenterwiseEscalation(Request $request)
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];

            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');

            $params['ATHfullname'] = Session::get('ATHfullname');
            $params['Dieticianfullname'] = Session::get('Dieticianfullname');
            $params['Memberfullname'] = Session::get('Memberfullname');
            $params['mobile_number'] = Session::get('mobile_number');

            // $data = $this->repository->getCenterwiseEscalation($params);
            $data = $this->repository->searchDataCenterwiseEscalation($params, $request->all());

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Centerwise-Escalation-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Centerwise Escalation list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {

                            $tempItem = (array) $item;

                            $requireExportData = [
//                                'index' => '',
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'ATHfullname' => ucwords($tempItem['ATHfullname']),
                                'Dieticianfullname' => ucwords($tempItem['Dieticianfullname']),
                                'Memberfullname' => ucwords($tempItem['Memberfullname']),
                                'mobile_number' => $tempItem['mobile_number'],
                                'EDate' => $tempItem['EDate'],
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'ATH Name', 'Dietician Name', 'Customer Name', 'Mobile No.', 'Escalation Date'));
                });
            })->export('xls');

            Session::forget('ATHfullname');
            Session::forget('Dieticianfullname');
            Session::forget('Memberfullname');
            Session::forget('mobile_number');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Detailed Sales Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/centerwise-escalation')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/centerwise-escalation')->with('msg-error', 'Error to export data.');
    }

    public function centerwiseNotification()
    {
        $cityList = $this->repository->getCityList()->toArray();
        $centerList = [];
        return view('admin::reports.centerwise-notification', compact('centerList', 'cityList'));
    }

    public function viewCenterwiseNotification(Request $request)
    {
        $params['center_id'] = $request->input('center_id');
        $params['city_id'] = $request->input('city_id');

        Session::forget('city_id');
        Session::forget('center_id');

        Session::set('city_id', $params['city_id']);
        Session::set('center_id', $params['center_id']);

        $params['Dieticianfullname'] = $request->input('Dieticianfullname');
        $params['Memberfullname'] = $request->input('Memberfullname');
        $params['mobile_number'] = $request->input('mobile_number');
        $params['NotiType'] = $request->input('NotiType');

        Session::forget('Dieticianfullname');
        Session::forget('Memberfullname');
        Session::forget('mobile_number');
        Session::forget('NotiType');

        if (isset($params['Dieticianfullname']) && !empty($params['Dieticianfullname'])) {
            Session::set('Dieticianfullname', $params['Dieticianfullname']);
        }

        if (isset($params['Memberfullname']) && !empty($params['Memberfullname'])) {
            Session::set('Memberfullname', $params['Memberfullname']);
        }

        if (isset($params['mobile_number']) && !empty($params['mobile_number'])) {
            Session::set('mobile_number', $params['mobile_number']);
        }

        if (isset($params['NotiType']) && !empty($params['NotiType'])) {
            Session::set('NotiType', $params['NotiType']);
        }


        $data = $this->repository->getCenterwiseNotification($params)->get();
        $data = collect($data);

        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $data = $data->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($data)
                ->addColumn('NotiType', function ($data) {
                    $message_type = '';

                    switch ($data->NotiType) {
                        case 1:
                            $message_type = 'General Notification';
                            break;
                        case 2:
                            $message_type = 'Activity Recommendation';
                            break;
                        case 3:
                            $message_type = 'Diet Recommendation';
                            break;
                        case 4:
                            $message_type = 'Session Recommendation';
                            break;
                        default:
                            $message_type = 'General Notification';
                    };

                    return $message_type;
                })
                ->addColumn('action', function ($data) {
                    $actionList = '';
                    return $actionList;
                })
                ->filter(function ($instance) use ($request) {

                    if ($request->has('Dieticianfullname')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->Dieticianfullname), strtolower($request->get('Dieticianfullname'))) ? true : false;
                        });
                    }
                    if ($request->has('Memberfullname')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->Memberfullname), strtolower($request->get('Memberfullname'))) ? true : false;
                        });
                    }
                    if ($request->has('mobile_number')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(($row->mobile_number), $request->get('mobile_number')) ? true : false;
                        });
                    }
                    if ($request->has('NotiType')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->NotiType), strtolower($request->get('NotiType'))) ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    //// Function to download centerwise notification
    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function downloadCenterwiseNotification(Request $request)
    {
        try {
            $fileFormat = 'csv';
            $validFormat = ['csv', 'xls', 'xlsx'];

            set_time_limit(0);
            $response = [];

            $params['center_id'] = Session::get('center_id');
            $params['city_id'] = Session::get('city_id');

            $params['Dieticianfullname'] = Session::get('Dieticianfullname');
            $params['Memberfullname'] = Session::get('Memberfullname');
            $params['mobile_number'] = Session::get('mobile_number');
            $params['NotiType'] = Session::get('NotiType');

            $data = $this->repository->searchDataCenterwiseNotification($params, $request->all());

            $uniqueTimeStr = Carbon::today()->toDateString();
            $fileName = "Centerwise-Notification-List-{$uniqueTimeStr}";

            Excel::create($fileName, function($excel) use ($data) {
                $excel->sheet('Centerwise Notification list', function($sheet) use($data) {

                    $data->chunk(250, function($records) use($sheet) {

                        $recordsData = array_map(function($item) {

                            $tempItem = (array) $item;

                            $message_type = '';

                            switch ($tempItem['NotiType']) {
                                case 1:
                                    $message_type = 'General Notification';
                                    break;
                                case 2:
                                    $message_type = 'Activity Recommendation';
                                    break;
                                case 3:
                                    $message_type = 'Diet Recommendation';
                                    break;
                                case 4:
                                    $message_type = 'Session Recommendation';
                                    break;
                                default:
                                    $message_type = 'General Notification';
                            };

                            $requireExportData = [
                                'city' => $tempItem['name'],
                                'center' => $tempItem['center_name'],
                                'Dieticianfullname' => ucwords($tempItem['Dieticianfullname']),
                                'Memberfullname' => ucwords($tempItem['Memberfullname']),
                                'mobile_number' => $tempItem['mobile_number'],
                                'NotiType' => ucwords($message_type),
                                'notification_count' => $tempItem['notification_count'],
                            ];

                            return $requireExportData;
                            $i++;
                        }, $records);

                        $sheet->fromArray($recordsData, null, 'A1', false, false);
                    });
// Add before first row
                    $sheet->prependRow(1, array('City', 'Center', 'Dietician Name', 'Customer Name', 'Mobile No.', 'Notification Type', 'Notification Count'));
                });
            })->export('xls');

            Session::forget('Dieticianfullname');
            Session::forget('Memberfullname');
            Session::forget('mobile_number');
            Session::forget('NotiType');
//            })->export($fileFormat);
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = "<b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-exported', ['name' => 'Detailed Sales Report']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            redirect('reports/centerwise-notification')->with('msg-error', 'Error to export data.');
        }
        redirect('reports/centerwise-notification')->with('msg-error', 'Error to export data.');
    }
}
