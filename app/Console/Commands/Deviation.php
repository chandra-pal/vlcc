<?php

namespace App\Console\Commands;

//use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use PDO;
use Log;
use DB;
use Exception;

class Deviation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deviation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to notify dietitian about the deviation';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $startTime = date('Y-m-d H:i:s');
            Log::info('command deviation Call.', ['comment' => "deviation service started at :" . $startTime]);
            DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $response = DB::select("CALL deviationService()");
            DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            if ($response[0]->response == 'SUCCESS') {
                $endTime = date('Y-m-d H:i:s');
                Log::info('command deviation Call.', ['comment' => "deviation service ended at :" . $endTime, 'Response' => $response[0]->response]);
            } else {
                throw new Exception('Something went wrong, please try again.');
            }
        } catch (Exception $e) {
            Log::info('command deviation Call.', ['comment' => $e->getMessage()]);
        }
    }

}
