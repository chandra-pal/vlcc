<?php
/**
 * The helper library class for getting information of a client from dietician ID
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Models\Member;
use Modules\Admin\Models\AdminCenters;
use Session;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Http\Request;

class MemberHelper
{

    /**
     * fetch user details
     * @return String
     */
    public function getUserWiseMemberList()
    {
        $membersRepository = new MembersRepository(new Member);
        $params = [];
        $membersList = [];
        $dieticienList = [];
        $memberContactList = [];
        $center_ids = [];

        $userInfoHelper = new UserInfoHelper();
        $user_center = $userInfoHelper->getLoggedInUserCenter(Auth::guard('admin')->user()->id);
        if (!empty($user_center)) {
            $center_ids = array_column($user_center, 'crm_center_id');
        }
        if (Auth::guard('admin')->user()->userType->id == 4 || Auth::guard('admin')->user()->userType->id == 8) {
            $params['username'] = Auth::guard('admin')->user()->username;
            //$membersList = $membersRepository->listMembersDataByDietician($params)->toArray();  
            // New Code => Display same members list on My Clients page & Customer Dropdown page
            // get members whose dietician_username = logged in user's username
            $first = Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->where('dietician_username', $params['username'])->where('status', 1)->get();


            // get all members of center of logged in dietician & where dietician_username is blank
            $second = Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->with('Centers')->orderBy('first_name')->where('dietician_username', '')->whereIn('crm_center_id', $center_ids)->where('status', 1)->get();

            $response = $first->merge($second); // Contains foo and bar.
            // get those members whose package is transferred in center of logged in user and base center is not equal to  center of logged in user
            $third = $membersRepository->getPackageTransferredMembers($center_ids);
            $result = $response->merge($third);
            $response = $result;

            $result = [];
            foreach ($response as $key => $value) {
                $name = $response[$key]["full_name"];
                $res[$response[$key]["id"]] = $name;
                $result = $res + $result;
            }
            $membersList = $result;
        } else if (Auth::guard('admin')->user()->userType->id == 1 || Auth::guard('admin')->user()->userType->id == 2) {
            $params['dietician_id'] = Auth::guard('admin')->user()->username;
            $membersList = $membersRepository->listMembersDataByDietician($params)->toArray();
            $dieticienList = [];
        }
        return $membersList;
    }

    public function getMemberDetailsById($id)
    {
        $member = Member::find($id)->toArray();
        $mobile_number = $member['mobile_number'];
        $memberListGlobal = Session::get('memberListGlobal');
        foreach ($memberListGlobal as $data) {
            if ($data['mobile_number'] == $mobile_number) {
                return $data;
            }
        }
    }

    public function getPackageList()
    {
        return $packageList = [
            "0" => [
                "package_name" => "Tummy Tuck",
                "package_order_id" => "CRM/23/231",
                "validity_date" => "27/07/2017"
            ],
            "1" => [
                "package_name" => "Body Firmer",
                "package_order_id" => "CRM/73/5791",
                "validity_date" => "26/08/2017"
            ]
        ];
    }

    public function getCentersList()
    {
        $user_id = Auth::guard('admin')->user()->id;
        $user_type_id = Auth::guard('admin')->user()->userType->id;
        $centersList = [];
        DB::setFetchMode(PDO::FETCH_KEY_PAIR);
        $centersList = DB::select("SELECT C1.center_id, C2.center_name FROM admin_centers C1 LEFT OUTER JOIN vlcc_centers C2 ON C1.center_id = C2.id WHERE C1.user_id=" . $user_id);
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $centersList;
    }

    //Function to display dietician's customer dropdown on session bookings page same as customers on my clients page
    public function getDieticianMemberList($dieticianCrmCenterId)
    {
        $params['username'] = Auth::guard('admin')->user()->username;

        $response = Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->where('dietician_username', $params['username'])->whereStatus("1")->get();

        $first = Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->where('dietician_username', $params['username'])->whereStatus("1")->get();

        $second = Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->where('dietician_username', '')->where('crm_center_id', $dieticianCrmCenterId)->where('status', 1)->get();

        $response = $first->merge($second);
        $result = collect($response)->toArray();
        $finalResult = array_column($result, 'full_name', 'id');

        return $finalResult;
    }
}
