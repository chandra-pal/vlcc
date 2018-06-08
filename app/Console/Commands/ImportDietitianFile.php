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

class ImportDietitianFile extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "import-dietitian-file {--file=''} {--usertype=''}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to import dietitians from csv along with customers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user, MembersRepository $memberRepository) {
//        try {
        $this->model = $user;
        $admin = new $this->model;

        $this->memberRepository = $memberRepository;


        $options = $this->option();


        $failed = array();
        $successfulCount = 0;
        $failedCount = 0;


        $startTime = date('Y-m-d H:i:s');
        Log::info('Command import admin csv :: ', ['comment' => "service started at :" . $startTime]);
        $this->info('Command import admin csv :: service started at : ' . $startTime);

        Excel::load($options['file'])->each(function (Collection $csvLine) use($admin, &$failed, &$successfulCount, &$failedCount) {

            $mobile = trim(substr($csvLine->get('mobile'), 0, 10));
            $name = trim($csvLine->get('name'));
            $nameArray = explode(" ", $name, 2);
            $usernameTrim = trim($csvLine->get('username'));
            $username = str_pad($usernameTrim, 4, '0', STR_PAD_LEFT);
            $designation = trim($csvLine->get('designation'));
            $email = (trim($csvLine->get('email')) != '') ? trim($csvLine->get('email')) : null;
            $crmCenterId = trim($csvLine->get('crm_center_id'));
            $genderCode = trim($csvLine->get('gender')) ? trim($csvLine->get('gender')) : "F";
            $gender = ($genderCode == "F") ? "0" : "1";
            $userRole = 0;
            switch (strtolower($designation)) {
                case 'dietician' :
                    $userRole = "4";
                    break;
                case 'physiotherapist':
                    $userRole = "5";
                    break;
                case 'doctor':
                    $userRole = "6";
                    break;
                case 'center head':
                    $userRole = "7";
                    break;
                case 'slimming head':
                    $userRole = "8";
                    break;
                case 'ath':
                    $userRole = "9";
                    break;
                case 'therapist':
                    $userRole = "10";
                    break;
		case 'center admin':
		    $userRole = "11";
		    break;
                default :
                    $userRole = "0";
            }
            if ($mobile && $designation && $username && $name && $userRole != 0) {
                $firstName = isset($nameArray[0]) ? trim($nameArray[0]) : "";
                $lastName = isset($nameArray[1]) ? trim($nameArray[1]) : "";

//$password = bcrypt($mobile);
                $password = bcrypt("7u8i9o0p");
                $save[] = $admin->updateOrCreate(
                        [
                    'username' => $username
                        ], [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'contact' => $mobile,
                    'password' => $password,
                    'user_type_id' => $userRole,
                    'skip_ip_check' => 1,
                    'gender' => $gender,
                    'created_by' => 1,
                    'updated_by' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            if (!empty($save)) {
                $this->comment("Imported " . $username . " successfully.");
//Log::info("Imported " . $username . " successfully.");
                $userId = (!empty($save[0]['id'])) ? $save[0]['id'] : DB::getPdo()->lastInsertId();
                if ($userRole != 0) {
                    $userLinkResponse = $this->addUserLinks($userId, $userRole);
                }

                $response = $this->memberRepository->importMemberData(['username' => $username]);

                $response['crm_center_id'] = (isset($response['crm_center_id']) && ($response['crm_center_id'] != "")) ? $response['crm_center_id'] : "";
                $crmCenterId = ($crmCenterId != "") ? $crmCenterId : $response['crm_center_id'];
                if ($crmCenterId != "") {
                    $crmCenterIdArr = explode(",", $crmCenterId);
                    foreach ($crmCenterIdArr as $crmCenterIdElet) {
                        $addCenterResponse = $this->addUserCenters($userId, $crmCenterIdElet);
                        if ($addCenterResponse->status == 0 && $addCenterResponse->lastInsertId == 0) {
                            $this->info("Center ". $crmCenterIdElet. " already exists or center insert failed.");
                        } else {
                            $this->info("Center ". $crmCenterIdElet. " added successfully.");
                        }
                    }
                }

                if (isset($response['status']) && $response['status'] == "error") {
                    $this->error("Error while importing customers for " . $username . ". " . $response['message']);
                    //Log::error("Error while importing customers for " . $username . ". " . $response['message']);
                } else {
                    $this->comment($response['message']);
                    /////Log::info($response['message']);
                }
                $successfulCount++;
            } else {
                $reason = array("Employee Code" => $username, "Name" => $name, "Mobile" => $mobile, "Designation" => $designation, "User Role" => $userRole);
                array_push($failed, ["mobile" => $mobile, "username" => $username, "name" => $name]);
                $failedCount++;
//Log::error("Error while importing " . $username . ". Data " . json_encode($reason));
                $this->error("Import failed for " . $username . ". Data " . json_encode($reason));
            }
        });

        $this->info("Importing Dietitians Finished. " . $successfulCount . " successful. " . $failedCount . " failed.");
        Log::info("Importing Dietitians Finished. " . $successfulCount . " successful. " . $failedCount . " failed.");

        $endTime = date('Y-m-d H:i:s');
        Log::info('Command import-customer-dietitian :: ', ['comment' => "service ended at :" . $endTime, 'Response' => 'Success']);
//        } catch (Exception $e) {
//            dd($e);
//            Log::info('Command import-customer-dietitian :: ', ['comment' => $e->getMessage()]);
//        }
    }

    /**
     * Function to add user links for newly created user
     * @param int $userId
     * @param int $userTypeId
     * @return array $response
     */
    private function addUserLinks($userId, $userTypeId) {
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $response = DB::select('CALL addUserLinksForDietician(' . $userId . ',' . $userTypeId . ')');
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $response[0]->response;
    }

    private function addUserCenters($userId, $centerId) {
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $response = DB::select('CALL insertAdminCenters(' . $userId . ',"' . $centerId . '")');
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $response[0];
    }

}
