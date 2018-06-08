<?php

namespace App\Api\Controllers;

use App\Api\Controllers\Controller;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\Member;
use App\Api\Transformers\PackagesTransformer;
use Modules\Admin\Models\MemberPackageServices;
use DB;
use Carbon\Carbon;
use Exception;
use Log;

class PackagesController extends Controller
{

    /**
     * Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function model()
    {
        return new MemberPackage;
    }

    /**
     * Transformer for the current model.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    protected function transformer()
    {
        return new PackagesTransformer;
    }

    public function store()
    {
        try {
            //$data = $this->request->json()->get($this->resourceKeySingular);
            $data = $this->request->json();
            // PackageHeaderID => crm_package_guid
            // PackageDetailID (Package execution ID) => crm_service_guid
            $requiredData = array('CLIENT_ID', 'PACKAGE_ID', 'START_DATE', 'END_DATE', 'FINAL_AMOUNT', 'PackageHeaderID', 'PackageDetailID', 'SERVICE_CATEGORY', 'SERVICE_ID', 'ServiceName', 'SERVICE_VALIDITY', 'Quantity', 'SERVICES_CONSUMED', 'CRM_MEMBER_GUID');
            foreach ($data as $dataitem) {
                $this->validate($dataitem, $requiredData);
            }
            $memberPackages = [];
            foreach ($data as $packageData) {
                $memberPackages = $packageData;
            }

            $current_time = Carbon::now();
            $file_name = date('Y-M-d');

            if (!empty($memberPackages)) {
                // Save request data into file
                //$request_file_name = $memberPackages["PackageHeaderID"] . "-" . Carbon::now();
                $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . $file_name . ".log", "a");
                $data = "\n\n" . $memberPackages["PackageHeaderID"] . "@" . $memberPackages["CRM_MEMBER_GUID"] . "\n";
                $data = $data . "******************************* Request ******************************************" . "\n";
                $data = $data . json_encode($memberPackages);
                fwrite($file, $data);
                fclose($file);

                //foreach ($data as $memberPackages) {
                $member_id = Member::select('id')->where("crm_member_guid", "=", $memberPackages['CRM_MEMBER_GUID'])->where("status", "=", 1)->first();

                if (isset($member_id['id'])) {
                    //$check = MemberPackage::select('id')->where("crm_package_guid", "=", $memberPackages['PackageHeaderID'])->where("member_id", "=", $member_id['id'])->first();
                    // Check if package guid exists in db or not
                    $check = MemberPackage::select('id')->where("crm_package_guid", "=", $memberPackages['PackageHeaderID'])->first();
                    $s_date = Carbon::createFromFormat('d/m/Y', $memberPackages['START_DATE']);
                    $start_date = $s_date->format('Y-m-d');

                    $e_date = Carbon::createFromFormat('d/m/Y', $memberPackages['END_DATE']);
                    $end_date = $e_date->format('Y-m-d');
                    $paymentMade = 0;

                    if (isset($memberPackages['PaidQuantity']) && $memberPackages['Quantity'] != 0) {
                        $paymentMade = ($memberPackages['FINAL_AMOUNT'] / $memberPackages['Quantity']) * $memberPackages['PaidQuantity'];
                    }

                    $input = "";
                    if (isset($memberPackages["AREA_SPECIFICATION1"]))
                        $input .= $memberPackages["AREA_SPECIFICATION1"];
                    if (isset($memberPackages["AREA_SPECIFICATION2"]))
                        $input .= "," . $memberPackages["AREA_SPECIFICATION2"];
                    if (isset($memberPackages["AREA_SPECIFICATION3"]))
                        $input .= "," . $memberPackages["AREA_SPECIFICATION3"];
                    $commaSeparatedAreaSpec = trim($input, ",");


                    //$crm_service_id = $memberPackages['PACKAGE_ID'] . '_' . 0;
                    $crm_service_guid = $memberPackages['PackageDetailID'];

                    /*                     * *** Insert or update package ****** */

                    if (!isset($check->id)) {
                        // Package does not exists, insert new package                        
                        $package_id = DB::table('member_packages')->insertGetId([
                            'member_id' => $member_id['id'],
                            'crm_package_id' => $memberPackages['PACKAGE_ID'],
                            'package_title' => $memberPackages['ServiceName'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'total_payment' => $memberPackages['FINAL_AMOUNT'],
                            'payment_made' => $paymentMade,
                            'created_by' => 1,
                            'created_at' => Carbon::now(),
                            'crm_package_guid' => $memberPackages['PackageHeaderID']
                        ]);
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Package added successfully.');
                    } else {
                        // Package already exists, update package
                        $s_date = Carbon::createFromFormat('d/m/Y', $memberPackages['START_DATE']);
                        $start_date = $s_date->format('Y-m-d');

                        $e_date = Carbon::createFromFormat('d/m/Y', $memberPackages['END_DATE']);
                        $end_date = $e_date->format('Y-m-d');

                        DB::table('member_packages')->where("crm_package_guid", "=", $memberPackages['PackageHeaderID'])->update([
                            'crm_package_id' => $memberPackages['PACKAGE_ID'],
                            'package_title' => $memberPackages['ServiceName'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'total_payment' => $memberPackages['FINAL_AMOUNT'],
                            'payment_made' => $paymentMade,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now(),
                            'crm_package_guid' => $memberPackages['PackageHeaderID']
                        ]);

                        $package_id = $check["id"];
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Package updated successfully.');
                    }

                    /*                     * * * Insert or update service * * * */
                    $checkService = MemberPackageServices::select('id')->where("crm_service_guid", "=", $memberPackages['PackageDetailID'])->first();
                    if (!isset($checkService)) {
                        $service_count = MemberPackageServices::select('id')->where('member_id', $member_id['id'])->where('package_id', $package_id)->count();
                        $crm_service_id = $memberPackages["PACKAGE_ID"] . "_" . $service_count;
                        // Insert service
                        DB::table('member_package_services')->insert([
                            'package_id' => $package_id,
                            'member_id' => $member_id['id'],
                            'crm_service_id' => $crm_service_id,
                            'service_name' => $memberPackages['ServiceName'],
                            'service_validity' => $memberPackages['SERVICE_VALIDITY'],
                            'services_booked' => $memberPackages['Quantity'],
                            'services_paid' => isset($memberPackages['PaidQuantity']) ? $memberPackages['PaidQuantity'] : 0,
                            'services_consumed' => $memberPackages['SERVICES_CONSUMED'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'created_by' => 1,
                            'created_at' => Carbon::now(),
                            'crm_service_guid' => $crm_service_guid,
                            'area_specification' => $commaSeparatedAreaSpec,
                            'service_category' => $memberPackages['SERVICE_CATEGORY'],
                            'service_code' => $memberPackages['SERVICE_ID']
                        ]);
                    } else {
                        // Update Service
                        DB::table('member_package_services')->where('id', $checkService->id)->update([
                            'package_id' => $package_id,
                            'member_id' => $member_id['id'],
                            //'crm_service_id' => $crm_service_id,
                            'crm_service_guid' => $crm_service_guid,
                            'area_specification' => $commaSeparatedAreaSpec,
                            'service_category' => $memberPackages['SERVICE_CATEGORY'],
                            'service_code' => $memberPackages['SERVICE_ID'],
                            'service_name' => $memberPackages['ServiceName'],
                            'service_validity' => $memberPackages['SERVICE_VALIDITY'],
                            'services_booked' => $memberPackages['Quantity'],
                            'services_paid' => isset($memberPackages['PaidQuantity']) ? $memberPackages['PaidQuantity'] : 0,
                            'services_consumed' => $memberPackages['SERVICES_CONSUMED'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now()]);
                    }
                } else {
                    $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Member does not exists.');
                }

                $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . date('Y-M-d') . ".log", "a");
                $data = "\n\n";
                $data = $data . "*****************************  Response ***********************************" . "\n";
                $data = $data . json_encode($response);
                fwrite($file, $data);
                fclose($file);
            }  // end of if packages array is not empty
            else {
                $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Technical error occured.');
                $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . date('Y-M-d') . ".log", "a");
                $data = "\n\n" . "****************************************************************************" . "\n";
                $data = $data . "Incorrect Json!";
                fwrite($file, $data);
                fclose($file);
            }
        } catch (Exception $e) {
            $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, $e->getMessage());
        }
//        if (!empty($memberPackages)) {
//            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . date('Y-M-d') . ".log", "a");
//            $data = "\n\n";
//            $data = $data . "*****************************  Response ***********************************" . "\n";
//            $data = $data . json_encode($response);
//            fwrite($file, $data);
//            fclose($file);
//        } else {
//            $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Incorrect Json!');
//            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . date('Y-M-d') . ".log", "a");
//            $data = "\n\n" . "****************************************************************************" . "\n";
//            $data = $data . "Incorrect Json!";
//            fwrite($file, $data);
//            fclose($file);
//        }
        return response()->json($response);
    }
}
