<?php

namespace App\Api\Controllers;

use App\Api\Controllers\Controller;
use Modules\Admin\Models\Member;
use App\Api\Transformers\membersTransformer;
use DB;
use Carbon\Carbon;
use App\Api\Controllers\ValidationController;
use Exception;
use Log;

class membersController extends Controller
{

    /**
     * Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function model()
    {
        return new Member;
    }

    /**
     * Transformer for the current model.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    protected function transformer()
    {
        return new membersTransformer;
    }

    public function store()
    {
        try {
            //$data = $this->request->json()->get($this->resourceKeySingular);
            $data = $this->request->json();
            $requiredData = array('CLIENT_ID', 'CRM_CENTER_ID', 'CLIENT_NAME', 'MOBILE_NO', 'CLIENT_SEX', 'CRM_MEMBER_GUID');
            foreach ($data as $dataitem) {
                $this->validate($dataitem, $requiredData);
            }

            $mobile_number = '';
            $crm_member_guid = '';
            $current_time = Carbon::now();
            $file_name = date('Y-M-d');


            foreach ($data as $k => $customerDetails) {

                $mobile_number = $customerDetails["MOBILE_NO"];
                $client_id = $customerDetails["CLIENT_ID"];
                $crm_member_guid = $customerDetails["CRM_MEMBER_GUID"];

                // Save request data into file
                $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_member_insertion_request_files/" . $file_name . ".log", "a");
                $data = "\n\n" . $customerDetails["MOBILE_NO"] . "-" . $customerDetails["CRM_MEMBER_GUID"] . "\n";
                $data = $data . "******************************* Request ******************************************" . "\n";
                $data = $data . json_encode($customerDetails);
                fwrite($file, $data);
                fclose($file);

                if ($customerDetails['MOBILE_NO'] != "" && $customerDetails['CRM_MEMBER_GUID'] != "" && $customerDetails['CRM_CENTER_ID'] != "") {
                    //DB::table('members')->where('mobile_number', $customerDetails['MOBILE_NO'])->where("crm_member_guid", "!=", $customerDetails['CRM_MEMBER_GUID'])->update(['status' => "0"]);

                    $check = Member::select('id', 'mobile_number', 'crm_member_guid')->where('crm_member_guid', trim($customerDetails['CRM_MEMBER_GUID']))->where("status", "=", 1)->first();
                    $gender = 2;
                    if($customerDetails["CLIENT_SEX"] == "Male") {
                        $gender = 1;
                    } 

                    if (!isset($check->id)) {
                        // Member does not exists , insert member
                        $memberId = DB::table('members')->insertGetId([
                            'crm_customer_id' => $customerDetails['CLIENT_ID'],
                            'crm_center_id' => $customerDetails['CRM_CENTER_ID'],
                            'crm_member_guid' => trim($customerDetails['CRM_MEMBER_GUID']),
                            'first_name' => $customerDetails['CLIENT_NAME'],
                            'last_name' => (isset($customerDetails['LAST_NAME'])) ? $customerDetails['LAST_NAME'] : '',
                            'date_of_birth' => (isset($customerDetails['DATE_OF_BIRTH'])) ? $customerDetails['DATE_OF_BIRTH'] : '',
                            'mobile_number' => $customerDetails['MOBILE_NO'],
                            'diet_plan_id' => 1,
                            'gender' => $gender,
                            'status' => 1,
                            'created_by' => 1,
                            'created_at' => $current_time
                        ]);
                        
//                        $membeCenterId = DB::table('member_centers')->insertGetId([
//                            'member_id' => $memberId,
//                            'crm_center_id' => $customerDetails['CRM_CENTER_ID'],
//                            'status' => 1
//                        ]);
                        
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Customer added successfully.');
                    } else {
                        // Member exists, update member
                        DB::table('members')->where('id', $check->id)->update([
                            'crm_customer_id' => $customerDetails['CLIENT_ID'],
                            'crm_center_id' => $customerDetails['CRM_CENTER_ID'],
                            'crm_member_guid' => trim($customerDetails['CRM_MEMBER_GUID']),
                            'first_name' => $customerDetails['CLIENT_NAME'],
                            'last_name' => (isset($customerDetails['LAST_NAME'])) ? $customerDetails['LAST_NAME'] : '',
                            'date_of_birth' => (isset($customerDetails['DATE_OF_BIRTH'])) ? $customerDetails['DATE_OF_BIRTH'] : '',
                            'mobile_number' => $customerDetails['MOBILE_NO'],
                            'gender' => $gender,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now()
                        ]);
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Customer updated successfully.');
                    }
                } else {
                    $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Technical error occured.');
                }
                //  $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Customer added successfully.');
            }
        } catch (Exception $e) {
            $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, $e->getMessage());
        }

        if (!empty($mobile_number) && !empty($crm_member_guid) && !empty($current_time)) {
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_member_insertion_request_files/" . date('Y-M-d') . ".log", "a");
            $data = "\n\n";
            $data = $data . "*****************************  Response ***********************************" . "\n";
            $data = $data . json_encode($response);
            fwrite($file, $data);
            fclose($file);
        } else {
            $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Incorrect Json!');
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_member_insertion_request_files/" . date('Y-M-d') . ".log", "a");
            $data = "\n\n" . "****************************************************************************" . "\n";
            $data = $data . "Incorrect Json!";
            fwrite($file, $data);
            fclose($file);
        }

        return response()->json($response);
    }
}
