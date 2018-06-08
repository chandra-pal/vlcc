<?php

namespace App\Console\Commands;

//use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use PDO;
use Log;
use DB;
use Exception;
use Modules\Admin\Models\User;
use Illuminate\Support\Collection;
use Excel;
use \Carbon\Carbon;
use Modules\Admin\Repositories\MembersRepository;
use Auth;
use Modules\Admin\Models\Member;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\MemberPackageServices;

class CustomerFileProcessList extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "process-user-files";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to import customers from files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user, MembersRepository $memberRepository) {
        //try {
        $params = array();
        $this->model = $user;
        $admin = new $this->model;

        $this->memberRepository = $memberRepository;

        $options = $this->option();

        // Grab all files from the desired folder
        $files = glob('/var/www/html/vlcc-admin/public/dataimported/*.*');
        $exclude_files = array('.', '..');
        if (!in_array($files, $exclude_files)) {
            // Sort files by modified time, latest to earliest
            // Use SORT_ASC in place of SORT_DESC for earliest to latest
            array_multisort(
                    array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC, $files
            );
        }

        $fileToPick = end($files);
        if ($fileToPick) {
            $handle = fopen($fileToPick, 'r');
            $data = fread($handle, filesize($fileToPick));
            $customerDataArray = json_decode($data, true);
            $created_by = isset(Auth::guard('admin')->user()->userType->id) ? Auth::guard('admin')->user()->userType->id : "1";

            $path_details = pathinfo($fileToPick);
            $fileNameFull = explode("-", $path_details['basename']);
            $fileName = $path_details['filename'] . "." . $path_details['extension'];
            if (sizeof($fileNameFull) == 2) {
                $crm_center_id = $fileNameFull[0];
                $fileNameSplit = explode(".", $fileNameFull[1]);
                $params['username'] = $fileNameSplit[0];

                foreach ($customerDataArray as $k => $customerDetails) {
                    if ($customerDetails['profile_data']['mobile_number'] != "") {
                        DB::table('members')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->where("crm_customer_id", "!=", $customerDetails['profile_data']['clientid'])->update([
                            'status' => "0"
                        ]);

                        $check = Member::select('id', 'mobile_number')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->where("crm_customer_id", "=", $customerDetails['profile_data']['clientid'])->whereStatus(1)->first();

                        $gender = (strtolower($customerDetails['profile_data']['gender'] === 'male')) ? 1 : 2;

                        $created_by = isset(Auth::guard('admin')->user()->userType->id) ? Auth::guard('admin')->user()->userType->id : "1";
                        if (!isset($check)) {
                            $memberId = DB::table('members')->insertGetId([
                                'crm_customer_id' => $customerDetails['profile_data']['clientid'],
                                'crm_center_id' => $crm_center_id, //$customerDataArray['response']['DieticianDetails']['center_id'],
                                'dietician_username' => $params['username'],
                                'first_name' => $customerDetails['profile_data']['first_name'],
                                'last_name' => (isset($customerDetails['profile_data']['last_name'])) ? $customerDetails['profile_data']['last_name'] : '',
                                'date_of_birth' => (isset($customerDetails['profile_data']['date_of_birth'])) ? $customerDetails['profile_data']['date_of_birth'] : '',
                                'mobile_number' => $customerDetails['profile_data']['mobile_number'],
                                'diet_plan_id' => 0,
                                'gender' => $gender,
                                'created_by' => $created_by,
                                'created_at' => Carbon::now()
                            ]);

                            if (!empty($customerDetails['package_data'])) {
                                foreach ($customerDetails['package_data'] as $i => $packageData) {
                                    $package_name = array();
                                    if (!empty($packageData['services'])) {
                                        foreach ($packageData['services'] as $services) {
                                            $services['service_name'] = isset($services['service_name']) ? $services['service_name'] : "";
                                            array_push($package_name, $services['service_name']);
                                        }

                                        if (count($package_name) > 1) {
                                            $package_name_string = implode(' + ', $package_name);
                                        } else {
                                            $package_name_string = implode('', $package_name);
                                        }
                                        $package_name = $package_name_string;

                                        $start_date = "";
                                        if (null != $customerDetails['profile_data']['start_date']) {
                                            $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                            if ($start_date != "false") {
                                                $start_date = $s_date->format('Y-m-d');
                                            }
                                        }

                                        $end_date = "";
                                        if (null != $customerDetails['profile_data']['end_date']) {
                                            $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                            if ($end_date != "false") {
                                                $end_date = $e_date->format('Y-m-d');
                                            }
                                        }

                                        $amount_paid = "0";
                                        $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                        $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                        if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                            $amount_paid = $total_amount - $amount_remaining;
                                        }

                                        $package_id = DB::table('member_packages')->insertGetId([
                                            'member_id' => $memberId,
                                            'crm_package_id' => $i,
                                            'package_title' => $package_name,
                                            'start_date' => $start_date,
                                            'end_date' => $end_date,
                                            'total_payment' => $total_amount,
                                            'payment_made' => $amount_paid,
                                            'created_by' => $created_by,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now()
                                        ]);
                                        foreach ($packageData['services'] as $k => $services) {
                                            $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];

                                            $crm_service_id = $i . '_' . $k;
                                            if (null != $services['service_start_date']) {
                                                $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                $serviceEndDate = clone $serviceStartDate;
                                                $serviceEndDate->addDays($services['service_validity']);
                                                $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                            } else {
                                                $serviceStartDate = "";
                                                $serviceEndDate = "";
                                            }

                                            DB::table('member_package_services')->insert([
                                                'package_id' => $package_id,
                                                'member_id' => $memberId,
                                                'crm_service_id' => $crm_service_id,
                                                'service_name' => $services['service_name'],
                                                'service_validity' => $services['service_validity'],
                                                'services_booked' => $services['total_services'],
                                                'services_consumed' => $services['services_consumed'],
                                                'start_date' => $serviceStartDate,
                                                'end_date' => $serviceEndDate,
                                                'created_by' => $created_by,
                                                'created_at' => Carbon::now(),
                                                'updated_at' => Carbon::now()
                                            ]);
                                        }
                                    }
                                }
                            }
                        } else {

                            DB::table('members')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->whereStatus(1)->update([
                                'crm_customer_id' => $customerDetails['profile_data']['clientid'],
                                'crm_center_id' => $crm_center_id, //$customerDataArray['response']['DieticianDetails']['center_id'],
                                'dietician_username' => $params['username'],
                                'first_name' => $customerDetails['profile_data']['first_name'],
                                'last_name' => (isset($customerDetails['profile_data']['last_name'])) ? $customerDetails['profile_data']['last_name'] : '',
                                'date_of_birth' => (isset($customerDetails['profile_data']['date_of_birth'])) ? $customerDetails['profile_data']['date_of_birth'] : '',
                                'mobile_number' => $customerDetails['profile_data']['mobile_number'],
                                'gender' => $gender,
                                'updated_by' => $created_by,
                                'updated_at' => Carbon::now()
                            ]);

                            $memberData = $check->toArray();
                            if (!empty($customerDetails['package_data'])) {
                                foreach ($customerDetails['package_data'] as $i => $packageData) {
                                    $checkMember = MemberPackage::select('id')->where('member_id', $memberData['id'])->where('crm_package_id', $i)->first();
                                    $package_name = array();
                                    if (empty($checkMember)) {
                                        if (!empty($packageData['services'])) {
                                            foreach ($packageData['services'] as $services) {
                                                $services['service_name'] = isset($services['service_name']) ? $services['service_name'] : "";
                                                array_push($package_name, $services['service_name']);
                                            }

                                            if (count($package_name) > 1) {
                                                $package_name_string = implode(' + ', $package_name);
                                            } else {
                                                $package_name_string = implode('', $package_name);
                                            }
                                            $package_name = $package_name_string;

                                            $start_date = "";
                                            if (null != $customerDetails['profile_data']['start_date']) {
                                                $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                                if ($start_date != "false") {
                                                    $start_date = $s_date->format('Y-m-d');
                                                }
                                            }

                                            $end_date = "";
                                            if (null != $customerDetails['profile_data']['end_date']) {
                                                $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                                if ($end_date != "false") {
                                                    $end_date = $e_date->format('Y-m-d');
                                                }
                                            }

                                            $amount_paid = "0";
                                            $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                            $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                            if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                                $amount_paid = $total_amount - $amount_remaining;
                                            }
                                            $package_id = DB::table('member_packages')->insertGetId([
                                                'member_id' => $memberData['id'],
                                                'crm_package_id' => $i,
                                                'package_title' => $package_name,
                                                'start_date' => $start_date,
                                                'end_date' => $end_date,
                                                'total_payment' => $total_amount,
                                                'payment_made' => $amount_paid,
                                                'created_by' => $created_by,
                                                'created_at' => Carbon::now(),
                                                'updated_at' => Carbon::now()
                                            ]);

                                            foreach ($packageData['services'] as $k => $services) {
                                                $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];
                                                $crm_service_id = $i . '_' . $k;
                                                if (null !== $services['service_start_date']) {
                                                    $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                    $serviceEndDate = clone $serviceStartDate;
                                                    $serviceEndDate->addDays($services['service_validity']);
                                                    $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                    $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                                } else {
                                                    $serviceStartDate = "";
                                                    $serviceEndDate = "";
                                                }
                                                DB::table('member_package_services')->insert([
                                                    'package_id' => $package_id,
                                                    'member_id' => $memberData['id'],
                                                    'crm_service_id' => $crm_service_id,
                                                    'service_name' => $services['service_name'],
                                                    'service_validity' => $services['service_validity'],
                                                    'services_booked' => $services['total_services'],
                                                    'services_consumed' => $services['services_consumed'],
                                                    'start_date' => $serviceStartDate,
                                                    'end_date' => $serviceEndDate,
                                                    'created_by' => $created_by,
                                                    'created_at' => Carbon::now(),
                                                    'updated_at' => Carbon::now()
                                                ]);
                                            }
                                        }
                                    } else {
                                        if (!empty($packageData['services'])) {
                                            foreach ($packageData['services'] as $services) {
                                                array_push($package_name, $services['service_name']);
                                            }

                                            if (count($package_name) > 1) {
                                                $package_name_string = implode(' + ', $package_name);
                                            } else {
                                                $package_name_string = implode('', $package_name);
                                            }
                                            $package_name = $package_name_string;

                                            $start_date = "";
                                            if (null != $customerDetails['profile_data']['start_date']) {
                                                $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                                if ($start_date != "false") {
                                                    $start_date = $s_date->format('Y-m-d');
                                                }
                                            }

                                            $end_date = "";
                                            if (null != $customerDetails['profile_data']['end_date']) {
                                                $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                                if ($end_date != "false") {
                                                    $end_date = $e_date->format('Y-m-d');
                                                }
                                            }

                                            $amount_paid = "0";
                                            $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                            $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                            if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                                $amount_paid = $total_amount - $amount_remaining;
                                            }

                                            DB::table('member_packages')->where('crm_package_id', $i)->update([
                                                'package_title' => $package_name,
                                                'start_date' => $start_date,
                                                'end_date' => $end_date,
                                                'total_payment' => $amount_paid,
                                                'payment_made' => $amount_paid,
                                                'updated_by' => $created_by,
                                                'updated_at' => Carbon::now()
                                            ]);
                                            foreach ($packageData['services'] as $k => $services) {
                                                $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];
                                                $crm_service_id = $i . '_' . $k;
                                                $checkService = MemberPackageServices::select('id')->where('member_id', $memberData['id'])->where('crm_service_id', $crm_service_id)->first();
                                                if (null != $services['service_start_date']) {
                                                    $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                    $serviceEndDate = clone $serviceStartDate;
                                                    $serviceEndDate->addDays($services['service_validity']);
                                                    $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                    $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                                } else {
                                                    $serviceStartDate = "";
                                                    $serviceEndDate = "";
                                                }
                                                if (empty($checkService)) {

                                                    DB::table('member_package_services')->insert([
                                                        'package_id' => $checkMember['id'],
                                                        'member_id' => $memberData['id'],
                                                        'crm_service_id' => $crm_service_id,
                                                        'service_name' => $services['service_name'],
                                                        'service_validity' => $services['service_validity'],
                                                        'services_booked' => $services['total_services'],
                                                        'services_consumed' => $services['services_consumed'],
                                                        'start_date' => $serviceStartDate,
                                                        'end_date' => $serviceEndDate,
                                                        'created_by' => $created_by,
                                                        'created_at' => Carbon::now(),
                                                        'updated_at' => Carbon::now()
                                                    ]);
                                                } else {
                                                    DB::table('member_package_services')->where('crm_service_id', $crm_service_id)->update([
                                                        'service_name' => $services['service_name'],
                                                        'service_validity' => $services['service_validity'],
                                                        'services_booked' => $services['total_services'],
                                                        'services_consumed' => $services['services_consumed'],
                                                        'start_date' => $serviceStartDate,
                                                        'end_date' => $serviceEndDate,
                                                        'updated_by' => $created_by,
                                                        'updated_at' => Carbon::now()
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //$customerCount++;
                    }
                }

                $this->comment("File " . $fileName . " processed successfully.");
                Log::info("File " . $fileName . " processed successfully.");
                rename('/var/www/html/vlcc-admin/public/dataimported/' . $fileName, '/var/www/html/vlcc-admin/public/dataprocessed/' . $fileName);
            } else {
                $this->comment("No files found.");
            }
        }
    }

}
