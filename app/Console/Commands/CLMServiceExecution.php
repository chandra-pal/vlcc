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
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Stream;
use Modules\Admin\Repositories\CPRRepository;

class CLMServiceExecution extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "process-service-execution";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to call CLM Service Execution API';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user, CPRRepository $cprRepository)
    {
        //try {
        $params = array();
        $this->model = $user;
        $admin = new $this->model;
        $this->cprRepository = $cprRepository;
        $options = $this->option();

        // Grab all files from the desired folder
        $files = glob('/var/www/html/vlcc-admin/public/service_execution_files/*.*');
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

            $path_details = pathinfo($fileToPick);
            $fileNameFull = explode("-", $path_details['basename']);
            $fileName = $path_details['filename'] . "." . $path_details['extension'];

            if (sizeof($fileNameFull) == 5) {
                $arr = explode(".", $fileName, 2);
                $member_session_record_id = explode("-", $arr[0])[4];
                $member_service_id = explode("-", $arr[0])[3];
                // Call CLM Service Execution API
                $client = new Client(['headers' => [ 'Content-Type' => 'application/json']]); //GuzzleHttp\Client
                if (config('app.env') == 'production') {
                    $crmBaseUrl = config('admin.clm_execution_base_url_prod');
                } else {
                    $crmBaseUrl = config('admin.clm_execution_base_url_dev');
                }
                $crmBaseUrl = $crmBaseUrl . "ServiceExecutionCreatio/CreateServiceExecution";
                $response = $client->post($crmBaseUrl, ['body' => $data]);
                $responseData = json_decode($response->getBody(), true);
                //$responseData["StatusCode"] = 200;

                // Create log file & write api response in log file
                $file = fopen("/var/www/html/vlcc-admin/storage/logs/service_execution_response/" . explode("-", $arr[0])[0] . "-" . explode("-", $arr[0])[1] . "-" . explode("-", $arr[0])[2] . "-" . explode("-", $arr[0])[3] . "-" . explode("-", $arr[0])[4] . ".log", "w");
                fwrite($file, json_encode($responseData));
                fclose($file);
                
                // If API success then delete file from folder

                if (isset($responseData["StatusCode"]) && !empty($responseData["StatusCode"]) && $responseData["StatusCode"] == 200) {
                    // delete file from folder
                    $service_executed_flag = 3;
                    // Update services_consumed
                    $this->updateServiceConsumed($member_service_id);
                    $this->comment("File " . $fileName . " processed successfully.");
                    Log::info("File " . $fileName . " processed successfully.");
                    rename('/var/www/html/vlcc-admin/public/service_execution_files/' . $fileName, '/var/www/html/vlcc-admin/public/service_execution_files_processed/' . $fileName);
                } else {
                    $service_executed_flag = 2;
                    $this->comment("Error while Processing File " . $fileName . "");
                    Log::info("Error while Processing File " . $fileName . "");
                    // delete file from folder
                    rename('/var/www/html/vlcc-admin/public/service_execution_files/' . $fileName, '/var/www/html/vlcc-admin/public/service_execution_files_failed/' . $fileName);
                }
                // Update Session programmer record service execution status
                $this->updateServiceExecutionStatus($member_session_record_id, $responseData,$member_service_id);
            } else {
                $this->comment("No files found.");
            }
        }
    }

    // Function to update service_executed flag 
    public function updateServiceExecutionFlag($id, $status)
    {
        $result = DB::update('UPDATE  member_session_record
        SET service_executed = ' . $status . ' WHERE id = "' . $id . '"');
        return $result;
    }
    
    // Function to update SERVICE_CONSUMED field 
    public function updateServiceConsumed($id)
    {
        $id = (int)$id;
        $result = DB::update('UPDATE  member_package_services
        SET services_consumed = services_consumed + 1 WHERE id = ' . $id . '');
    }

    // Function to update service_execution status
    public function updateServiceExecutionStatus($member_session_record_id, $responseData, $service_id) {
        $result = $this->cprRepository->getMemberSessionRecordData($member_session_record_id);
        $session_data = $this->cprRepository->getMemberSessionData($result->session_id);
        $service_execution_info[0]["service_id"] = $service_id;
        $service_execution_info[0]["code"] = isset($responseData["StatusCode"]) ? $responseData["StatusCode"] : 0;
        $service_execution_info[0]["message"] = isset($responseData["Header"]) ? $responseData["Header"] : 'empty';
        $update_array = $service_execution_info;
        if (!empty($result->service_execution_status)) {
            $service_execution_status = json_decode($result->service_execution_status, true);
            $check = array_column($service_execution_status, "service_id");
            $foundKey = array_search($service_id, $check);
            if ($foundKey === false) {
                
            } else {
                unset($service_execution_status[$foundKey]);
            }
            $update_array = array_merge($service_execution_info, $service_execution_status);
        }
        $result = DB::update("UPDATE  member_session_record
        SET service_execution_status = '" . json_encode($update_array) . "' WHERE id='" . $member_session_record_id . "'");
        if(count($update_array) == count(explode(",", $session_data->service_id))) {
           $this->cprRepository->updateServiceExecutedFlag($member_session_record_id, 1); 
        }
    }
}
