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

class ImportAdminCsv extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = "import {--usertype=''}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to import dietitians from csv';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user) {
        try {
            $this->model = $user;
            $admin = new $this->model;


            $options = $this->option();
            //$this->info($options['usertype']);

            $startTime = date('Y-m-d H:i:s');
            Log::info('Command import admin csv :: ', ['comment' => "service started at :" . $startTime]);

            Excel::load('dietitian_list.csv')->each(function (Collection $csvLine) use($admin) {
                $mobile = substr($csvLine->get('mobile'), 0, 10);

                if ($mobile) {

                    $name = $csvLine->get('name');
                    $nameArray = explode(" ", $name);

                    $firstName = isset($nameArray[0]) ? $nameArray[0] : "";
                    $lastName = isset($nameArray[1]) ? $nameArray[1] : "";

                    //$password = bcrypt($mobile);
                    $password = bcrypt("7u8i9o0p");


                    $save[] = $admin->updateOrCreate(
                            [
                        'username' => $csvLine->get('username')
                            ], [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $csvLine->get('username'),
                        'contact' => $mobile,
                        'password' => $password,
                        'user_type_id' => "4",
                        'skip_ip_check' => 1,
                        'created_by' => 1,
                        'updated_by' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                if (!empty($save)) {
                    $userId = (!empty($save[0]['id'])) ? $save[0]['id'] : DB::getPdo()->lastInsertId();
                    $userTypeId = (!empty($save[0]['user_type_id'])) ? $save[0]['user_type_id'] : 4;
                    $userLinkResponse = $this->addUserLinks($userId, $userTypeId);
                    $this->comment("Imported " . $csvLine->get('username') . " successfully.");
                } else {
                    $this->error("Import failed " . $csvLine->get('username'));
                }
            });

            $response = "Success";
            $this->info("Importing Dietitians");
            if ($response[0]->response == 'SUCCESS') {
                $endTime = date('Y-m-d H:i:s');
                Log::info('Command import admin csv :: ', ['comment' => "service ended at :" . $endTime, 'Response' => $response[0]->response]);
            } else {
                throw new Exception('Something went wrong, please try again.');
            }
        } catch (Exception $e) {
            Log::info('command deviation Call.', ['comment' => $e->getMessage()]);
        }
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

}
