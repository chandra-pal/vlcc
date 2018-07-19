<?php

namespace App\Api\Controllers;

use App\Api\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Exception;
use Log;

class SessionBookingController extends Controller
{

    /**
     * Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function model()
    {
        //return new MemberPackage;
    }

    /**
     * Transformer for the current model.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    protected function transformer()
    {
        //return new PackagesTransformer;
    }

    public function store()
    {
        try {
            //$data = $this->request->json()->get($this->resourceKeySingular);
            $data = $this->request->json();
            $requiredData = array('mobile_number', 'service_code', 'session_date', 'start_time', 'end_time', 'crm_center_id');
            foreach ($data as $dataitem) {
                $this->validate($dataitem, $requiredData);
            }
            foreach ($data as $sessionData) {
                $session = $sessionData;
            }
            
            print_r($session);
            die;
            
            if (!empty($session)) {
                //foreach ($data as $memberPackages) {
                $member_id = Member::select('id')->where("crm_customer_id", "=", $memberPackages['CLIENT_ID'])->whereStatus(1)->first();

                if (isset($member_id['id'])) {
                    $check = MemberPackage::select('id')->where("crm_package_id", "=", $memberPackages['PACKAGE_ID'])->where("member_id", "=", $member_id['id'])->first();

                    $s_date = Carbon::createFromFormat('d/m/Y', $memberPackages['START_DATE']);
                    $start_date = $s_date->format('Y-m-d');

                    $e_date = Carbon::createFromFormat('d/m/Y', $memberPackages['END_DATE']);
                    $end_date = $e_date->format('Y-m-d');
                    $paymentMade = 0;

                    if (isset($memberPackages['PaidQuantity'])) {
                        $paymentMade = ($memberPackages['FINAL_AMOUNT'] / $memberPackages['Quantity']) * $memberPackages['PaidQuantity'];
                    }

                    $input = "";
                    if (isset($memberPackages["AreaSpecification1"]))
                        $input .= $memberPackages["AreaSpecification1"];
                    if (isset($memberPackages["AreaSpecification2"]))
                        $input .= "," . $memberPackages["AreaSpecification2"];
                    if (isset($memberPackages["AreaSpecification3"]))
                        $input .= "," . $memberPackages["AreaSpecification3"];
                    $commaSeparatedAreaSpec = $input;

                    $crm_service_id = $memberPackages['PACKAGE_ID'] . '_' . 0;
                    $crm_service_guid = $memberPackages['PackageDetailID'];

                    if (!isset($check)) {
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

                        // Services Insert code
                        $memberPackages['SERVICE_VALIDITY'] = ($memberPackages['SERVICE_VALIDITY'] == null) ? "" : $memberPackages['SERVICE_VALIDITY'];

                        DB::table('member_package_services')->insert([
                            'package_id' => $package_id,
                            'member_id' => $member_id['id'],
                            'crm_service_id' => $crm_service_id,
                            'service_name' => $memberPackages['ServiceName'],
                            'service_validity' => $memberPackages['SERVICE_VALIDITY'],
                            'services_booked' => $memberPackages['Quantity'],
                            'services_consumed' => $memberPackages['SERVICES_CONSUMED'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'created_by' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'crm_service_guid' => $crm_service_guid,
                            'area_specification' => $commaSeparatedAreaSpec,
                            'service_category' => $memberPackages['SERVICE_CATEGORY'],
                            'service_code' => $memberPackages['SERVICE_ID']
                        ]);
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Package added successfully.');
                    } else {
                        // Package already exists, update package
                        $s_date = Carbon::createFromFormat('d/m/Y', $memberPackages['START_DATE']);
                        $start_date = $s_date->format('Y-m-d');

                        $e_date = Carbon::createFromFormat('d/m/Y', $memberPackages['END_DATE']);
                        $end_date = $e_date->format('Y-m-d');

                        DB::table('member_packages')->where('member_id', $member_id['id'])->where("crm_package_id", "=", $memberPackages['PACKAGE_ID'])->update([
                            'package_title' => $memberPackages['ServiceName'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'total_payment' => $memberPackages['FINAL_AMOUNT'],
                            'payment_made' => $paymentMade,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now(),
                            'crm_package_guid' => $memberPackages['PackageHeaderID']
                        ]);

                        // foreach ($memberPackages['SERVICES'] as $k => $services) {
                        //$input = array_splice($memberPackages, -3);
                        //$commaSeparatedAreaSpec = implode(",", $input);
//
                        $memberPackages['SERVICE_VALIDITY'] = ($memberPackages['SERVICE_VALIDITY'] == null) ? "" : $memberPackages['SERVICE_VALIDITY'];
                        //$crm_service_id = $memberPackages['PACKAGE_ID'];
                        $crm_service_id = $memberPackages['PACKAGE_ID'] . '_' . 0;
                        $checkService = MemberPackageServices::select('id')->where('member_id', $member_id['id'])->where('package_id', $check->id)->where('service_name', $memberPackages['ServiceName'])->first();

                        /* if (null != $memberPackages['SERVICE_START_DATE']) {
                          $serviceStartDate = Carbon::createFromFormat('d/m/Y', $memberPackages['START_DATE']);
                          $serviceEndDate = clone $serviceStartDate;
                          $serviceEndDate->addDays($memberPackages['SERVICE_VALIDITY']);
                          $serviceEndDate = $serviceEndDate->format('Y-m-d');
                          $serviceStartDate = $serviceStartDate->format('Y-m-d');
                          } else {
                          $serviceStartDate = "";
                          $serviceEndDate = "";
                          } */

                        if (empty($checkService)) {
                            // query to get count of package services
                            $service_count = MemberPackageServices::select('id')->where('member_id', $member_id['id'])->where('package_id', $check->id)->count();
                            $crm_service_id = $memberPackages["PACKAGE_ID"] . "_" . $service_count;
                            // Insert Service
                            DB::table('member_package_services')->insert([
                                'package_id' => $check->id,
                                'member_id' => $member_id['id'],
                                'crm_service_id' => $crm_service_id,
                                'service_name' => $memberPackages['ServiceName'],
                                'service_validity' => $memberPackages['SERVICE_VALIDITY'],
                                'services_booked' => $memberPackages['Quantity'],
                                'services_consumed' => $memberPackages['SERVICES_CONSUMED'],
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'created_by' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'crm_service_guid' => $crm_service_guid,
                                'area_specification' => $commaSeparatedAreaSpec,
                                'service_category' => $memberPackages['SERVICE_CATEGORY'],
                                'service_code' => $memberPackages['SERVICE_ID']
                            ]);
                        } else {
                            DB::table('member_package_services')->where('id', $checkService->id)->update([
                                'service_name' => $memberPackages['ServiceName'],
                                'service_validity' => $memberPackages['SERVICE_VALIDITY'],
                                'services_booked' => $memberPackages['Quantity'],
                                'services_consumed' => $memberPackages['SERVICES_CONSUMED'],
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'updated_by' => 1,
                                'updated_at' => Carbon::now(),
                                'crm_service_guid' => $crm_service_guid,
                                'area_specification' => $commaSeparatedAreaSpec,
                                'service_category' => $memberPackages['SERVICE_CATEGORY'],
                                'service_code' => $memberPackages['SERVICE_ID']
                            ]);
                            //}
                        }
                        $response = $this->getResponse('Success', parent::SUCCESS_RESPONSE_CODE, null, 'Package updated successfully.');
                    }
                } else {
                    $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Member does not exists.');
                }
            }  // end of if packages array is not empty
            else {
                $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, 'Technical error occured.');
            }
        } catch (Exception $e) {
            $response = $this->getResponse('error', parent::VALIDATION_FAIL, null, $e->getMessage());
        }
        return response()->json($response);
    }
}
