<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class UpdateSessionStatus extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-session-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to update session status as "Cancelled" & update cancellation comment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $startTime = date('Y-m-d H:i:s');
            Log::info('command update-session-status Call.', ['comment' => "update session status started at :" . $startTime]);
            $todaysDate = date('Y-m-d');
            $result = $this->updateSessionStatus($todaysDate);
            
            if($result>0) {
                // Updated Sessions with Status "Cancelled"
            } else {
                //No sessions Updated
            }
            $endTime = date('Y-m-d H:i:s');
                Log::info('command update-session-status Call.', ['comment' => "update-session-status service ended at :" . $endTime]);
        } catch (Exception $e) {
            Log::info('command update-session-status Call.', ['comment' => $e->getMessage()]);
        }
    }

    // Function to retreive Members with Diet Log count(daywise) for Diet Diary Reminder Service
    public function updateSessionStatus($todaysDate) {
        $result = DB::update('UPDATE  member_session_bookings 
        SET cancellation_comment = CASE WHEN STATUS = "1" THEN "Not responded by dietician" ELSE "Not Attended" END, 
        status=4 WHERE session_date = "'.$todaysDate.'" AND (status="1" OR status="2")');
        return $result;
    }
}
