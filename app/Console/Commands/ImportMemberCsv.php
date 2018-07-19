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

class ImportMemberCsv extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "import-member-csv";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron to import members from csv file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Log::info('command import-member-csv Call.', ['comment' => "import members csv service started at :" . date('Y-m-d H:i:s')]);
            $file = public_path('import_csv/India_import_member.csv');
            $memberArr = $this->csvToArray($file);
            $response = $this->importCsv($memberArr);
            if ($response)
                Log::info('command import-member-csv Call.', ['comment' => "create/edit members service ended at :" . date('Y-m-d H:i:s'), 'Processed Records' => count($memberArr)]);
            else
                throw new Exception('Something went wrong, Please try again');
        } catch (Exception $e) {
            Log::info('command import-member-csv Call.', ['errorCode' => $e->getCode(), 'errorMessage' => $e->getMessage()]);
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
     * @param array $memberArr
     * @return boolean
     */
    private function importCsv($memberArr)
    {
        $file_name = date('Y-M-d') . "-cron";
        foreach ($memberArr as $key => $member) {
            
            $mobile_number = $member["MOBILE_NO"];
            $client_id = $member["CLIENT_ID"];
            $crm_member_guid = $member["CRM_MEMBER_GUID"];

            // Save request data into file
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_member_insertion_request_files/" . $file_name . ".log", "a");
            $data = "\n\n" . $member["MOBILE_NO"] . "-" . $member["CRM_MEMBER_GUID"] . "\n";
            $data = $data . "******************************* Request ******************************************" . "\n";
            $data = $data . json_encode($member);
            fwrite($file, $data);
            fclose($file);

            if ($member['CLIENT_ID'] != "" && $member['CRM_CENTER_ID'] != "" && $member['CLIENT_NAME'] != "" && $member['MOBILE_NO'] != "" && $member['CLIENT_SEX'] != "" && $member['CRM_MEMBER_GUID'] != "") {

                $check = Member::select('id', 'mobile_number', 'crm_member_guid')->where('crm_member_guid', trim($member['CRM_MEMBER_GUID']))->where("status", "=", 1)->first();
                $gender = 2;
                if ($member["CLIENT_SEX"] == "Male") {
                    $gender = 1;
                }

                $date_of_birth = ($member["DATE_OF_BIRTH"] != "") ? Carbon::createFromFormat('d/m/Y', $member['DATE_OF_BIRTH'])->format('Y-m-d') : '';

                if (!isset($check->id)) {
                    // Member does not exists , insert member
                    $memberId = DB::table('members')->insertGetId([
                        'crm_customer_id' => $member['CLIENT_ID'],
                        'crm_center_id' => $member['CRM_CENTER_ID'],
                        'crm_member_guid' => trim($member['CRM_MEMBER_GUID']),
                        'first_name' => $member['CLIENT_NAME'],
                        'last_name' => (isset($member['LAST_NAME'])) ? $member['LAST_NAME'] : '',
                        'date_of_birth' => $date_of_birth,
                        'mobile_number' => $member['MOBILE_NO'],
                        'diet_plan_id' => 1,
                        'gender' => $gender,
                        'status' => 1,
                        'created_by' => 1,
                        'created_at' => Carbon::now()
                    ]);
                    $response_msg = 'Customer added successfully.';
                    $this->info("Customer Mobile No : " . $member['MOBILE_NO'] . " with Guid :  " . $member['CRM_MEMBER_GUID'] . " added successfully");
                } else {
                    // Member exists, update member
                    DB::table('members')->where('id', $check->id)->update([
                        'crm_customer_id' => $member['CLIENT_ID'],
                        'crm_center_id' => $member['CRM_CENTER_ID'],
                        'crm_member_guid' => trim($member['CRM_MEMBER_GUID']),
                        'first_name' => $member['CLIENT_NAME'],
                        'last_name' => (isset($member['LAST_NAME'])) ? $member['LAST_NAME'] : '',
                        'date_of_birth' => $date_of_birth,
                        'mobile_number' => $member['MOBILE_NO'],
                        'gender' => $gender,
                        'updated_by' => 1,
                        'updated_at' => Carbon::now()
                    ]);
                    $response_msg = 'Customer updated successfully.';
                    $this->info("Customer Mobile No : " . $member['MOBILE_NO'] . " with Guid :  " . $member['CRM_MEMBER_GUID'] . " updated successfully");
                }
            } else {
                $response_msg = 'Technical error occured.';
                $this->info("Customer Mobile No : " . $member['MOBILE_NO'] . " with Guid :  " . $member['CRM_MEMBER_GUID'] . " failed with technical error.");
            }

            // Save response into file
            $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_member_insertion_request_files/" . date('Y-M-d') . "-cron.log", "a");
            $data = "\n\n";
            $data = $data . "*****************************  Response ***********************************" . "\n";
            $data = $data . $response_msg;
            fwrite($file, $data);
            fclose($file);
        }
        return true;
    }
}
