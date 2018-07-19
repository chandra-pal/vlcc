<?php

namespace App\Console\Commands;

//use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use PDO;
use Log;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Excel;
use \Carbon\Carbon;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Stream;
use Modules\Admin\Models\Member;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\MemberPackageServices;

class ImportPackageCsv extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "import-package-csv";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron to import packages from csv file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Log::info('command import-package-csv Call.', ['comment' => "import packages csv service started at :" . date('Y-m-d H:i:s')]);
            $file = public_path('import_csv/India_import_package.csv');
            $packageArr = $this->csvToArray($file);
            $response = $this->importCsv($packageArr);
            if ($response)
                Log::info('command import-package-csv Call.', ['comment' => "import-package-csv service ended at :" . date('Y-m-d H:i:s'), 'Processed Records' => count($packageArr)]);
            else
                throw new Exception('Something went wrong, Please try again');
        } catch (Exception $e) {
            Log::info('command import-package-csv Call.', ['errorCode' => $e->getCode(), 'errorMessage' => $e->getMessage()]);
        }
    }

    /**
     * Function to read csv and convert to array
     * @param string $filename
     * @param char $delimiter
     * @return array
     */
    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (array(null) !== $row) {
                    if (!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Function to create member if not exist
     * @param array $packageArr
     * @return boolean
     */
    private function importCsv($packageArr)
    {
        $file_name = date('Y-M-d') . "-cron";
        foreach ($packageArr as $key => $package) {

            // Save request data into file
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . $file_name . ".log", "a");
            $data = "\n\n" . $package["PackageHeaderID"] . "@" . $package["CRM_MEMBER_GUID"] . "\n";
            $data = $data . "******************************* Request ******************************************" . "\n";
            $data = $data . json_encode($package);
            fwrite($file, $data);
            fclose($file);

            if ($package['CLIENT_ID'] != "" && $package['PACKAGE_ID'] != "" && $package['START_DATE'] != "" && $package['END_DATE'] != "" && $package['FINAL_AMOUNT'] != "" && $package['PackageHeaderID'] != "" && $package['PackageDetailID'] != "" && $package['SERVICE_CATEGORY'] != "" && $package['SERVICE_ID'] != "" && $package['ServiceName'] != "" && $package['SERVICE_VALIDITY'] != "" && $package['Quantity'] != "" && $package['SERVICES_CONSUMED'] != "" && $package['CRM_MEMBER_GUID']) {

                $member_data = Member::select('id')->where("crm_member_guid", "=", $package['CRM_MEMBER_GUID'])->where("status", "=", 1)->first();

                if (isset($member_data['id'])) {
                    // Check if package guid exists in db or not
                    $check = MemberPackage::select('id')->where("crm_package_guid", "=", $package['PackageHeaderID'])->first();
                    $s_date = Carbon::createFromFormat('d/m/Y', $package['START_DATE']);
                    $start_date = $s_date->format('Y-m-d');

                    $e_date = Carbon::createFromFormat('d/m/Y', $package['END_DATE']);
                    $end_date = $e_date->format('Y-m-d');
                    $paymentMade = 0;

                    if (isset($package['PaidQuantity']) && $package['Quantity'] != 0) {
                        $paymentMade = ($package['FINAL_AMOUNT'] / $package['Quantity']) * $package['PaidQuantity'];
                    }

                    $input = "";
                    if (isset($package["AREA_SPECIFICATION1"]))
                        $input .= $package["AREA_SPECIFICATION1"];
                    if (isset($package["AREA_SPECIFICATION2"]))
                        $input .= "," . $package["AREA_SPECIFICATION2"];
                    if (isset($package["AREA_SPECIFICATION3"]))
                        $input .= "," . $package["AREA_SPECIFICATION3"];
                    $commaSeparatedAreaSpec = trim($input, ",");


                    //$crm_service_id = $package['PACKAGE_ID'] . '_' . 0;
                    $crm_service_guid = $package['PackageDetailID'];

                    /* * *** Insert or update package ****** */

                    if (!isset($check->id)) {
                        // Package does not exists, insert new package                        
                        $package_id = DB::table('member_packages')->insertGetId([
                            'member_id' => $member_data['id'],
                            'crm_package_id' => $package['PACKAGE_ID'],
                            'package_title' => $package['ServiceName'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'total_payment' => $package['FINAL_AMOUNT'],
                            'payment_made' => $paymentMade,
                            'created_by' => 1,
                            'created_at' => Carbon::now(),
                            'crm_package_guid' => $package['PackageHeaderID']
                        ]);
                        $response_msg = 'Package added successfully.';
                        $this->info("Package Id : " . $package['PACKAGE_ID'] . " with Guid :  " . $package['PackageHeaderID'] . " added successfully.");
                    } else {
                        // Package already exists, update package
                        $s_date = Carbon::createFromFormat('d/m/Y', $package['START_DATE']);
                        $start_date = $s_date->format('Y-m-d');

                        $e_date = Carbon::createFromFormat('d/m/Y', $package['END_DATE']);
                        $end_date = $e_date->format('Y-m-d');

                        DB::table('member_packages')->where("crm_package_guid", "=", $package['PackageHeaderID'])->update([
                            'crm_package_id' => $package['PACKAGE_ID'],
                            'package_title' => $package['ServiceName'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'total_payment' => $package['FINAL_AMOUNT'],
                            'payment_made' => $paymentMade,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now(),
                            'crm_package_guid' => $package['PackageHeaderID']
                        ]);

                        $package_id = $check["id"];
                        $response_msg = 'Package updated successfully.';
                        $this->info("Package Id : " . $package['PACKAGE_ID'] . " with Guid :  " . $package['PackageHeaderID'] . " updated successfully.");
                    }

                    /*                     * * * Insert or update service * * * */
                    $checkService = MemberPackageServices::select('id')->where("crm_service_guid", "=", $package['PackageDetailID'])->first();
                    if (!isset($checkService)) {
                        $service_count = MemberPackageServices::select('id')->where('member_id', $member_data['id'])->where('package_id', $package_id)->count();
                        $crm_service_id = $package["PACKAGE_ID"] . "_" . $service_count;
                        // Insert service
                        DB::table('member_package_services')->insert([
                            'package_id' => $package_id,
                            'member_id' => $member_data['id'],
                            'crm_service_id' => $crm_service_id,
                            'service_name' => $package['ServiceName'],
                            'service_validity' => $package['SERVICE_VALIDITY'],
                            'services_booked' => $package['Quantity'],
                            'services_paid' => isset($package['PaidQuantity']) ? $package['PaidQuantity'] : 0,
                            'services_consumed' => $package['SERVICES_CONSUMED'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'created_by' => 1,
                            'created_at' => Carbon::now(),
                            'crm_service_guid' => $crm_service_guid,
                            'area_specification' => $commaSeparatedAreaSpec,
                            'service_category' => $package['SERVICE_CATEGORY'],
                            'service_code' => $package['SERVICE_ID']
                        ]);
                    } else {
                        // Update Service
                        DB::table('member_package_services')->where('id', $checkService->id)->update([
                            'package_id' => $package_id,
                            'member_id' => $member_data['id'],
                            //'crm_service_id' => $crm_service_id,
                            'crm_service_guid' => $crm_service_guid,
                            'area_specification' => $commaSeparatedAreaSpec,
                            'service_category' => $package['SERVICE_CATEGORY'],
                            'service_code' => $package['SERVICE_ID'],
                            'service_name' => $package['ServiceName'],
                            'service_validity' => $package['SERVICE_VALIDITY'],
                            'services_booked' => $package['Quantity'],
                            'services_paid' => isset($package['PaidQuantity']) ? $package['PaidQuantity'] : 0,
                            'services_consumed' => $package['SERVICES_CONSUMED'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'updated_by' => 1,
                            'updated_at' => Carbon::now()]);
                    }
                } else {
                    $response_msg = 'Member does not exists.';
                    $this->info("Package Id : " . $package['PACKAGE_ID'] . " with Guid :  " . $package['PackageHeaderID'] . " failed with member does not exists.");
                }
            } else {
                $response_msg = 'Technical error occured.';
                $this->info("Package Id : " . $package['PACKAGE_ID'] . " with Guid :  " . $package['PackageHeaderID'] . " failed with technical error.");
            }
            
            // Save response into file
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . date('Y-M-d') . "-cron.log", "a");
            $data = "\n\n";
            $data = $data . "*****************************  Response ***********************************" . "\n";
            $data = $data . $response_msg;
            fwrite($file, $data);
            fclose($file);
        }
        return true;
    }
}
