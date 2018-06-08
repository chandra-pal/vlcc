<?php

namespace App\Console\Commands;

//use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use Modules\Admin\Models\User;
use Modules\Admin\Models\UserType;
use PDO;
use Log;
use DB;
use Exception;

class CreateDietician extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-dietician';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to read excel and add dietician';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            Log::info('command create-dietician Call.', ['comment' => "create dietician service started at :" . date('Y-m-d H:i:s')]);
            $file = public_path('dieticians/dietician_list.csv');
            $dieticianArr = $this->csvToArray($file);
            $response = $this->importCsv($dieticianArr);
            if ($response)
                Log::info('command create-dietician Call.', ['comment' => "create dietician service ended at :" . date('Y-m-d H:i:s'), 'Processed Records' => count($dieticianArr)]);
            else
                throw new Exception('Something went wrong, Please try again');
        } catch (Exception $e) {
            Log::info('command create-dietician Call.', ['errorCode' => $e->getCode(), 'errorMessage' => $e->getMessage()]);
        }
    }

    /**
     * Function to read csv and convert to array
     * @param string $filename
     * @param char $delimiter
     * @return array
     */
    private function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Function to create dietician if not exist
     * @param array $dieticianArr
     * @return boolean
     */
    private function importCsv($dieticianArr) {
        foreach ($dieticianArr as $key => $dietician) {
            $password = str_random(8);
            $dietician['password'] = bcrypt($password);
            $dietician['created_by'] = 1;
            $dietician['user_type_id'] = 4;
            if (!User::where('username', '=', $dietician['username'])->exists()) {
                $user = User::create($dietician);
                $userType = UserType::find($dietician['user_type_id']);
                $user->userType()->associate($userType);
                $user->password = $dietician['password'];
                $user->save();
                $userLinkResponse = $this->addUserLinks($user->id, $user->user_type_id);
            }
        }
        return true;
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
