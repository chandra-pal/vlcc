<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use Log;
use DB;
use Exception;
use Modules\Admin\Services\Helper\PushHelper;
use Modules\Admin\Models\Recommendation;

class DietDiaryReminder extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diet-diary-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to notify member about the diet diary reminder.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Recommendation $recommendation)
    {
        parent::__construct();
        $this->model = $recommendation;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $todaysDate = date('Y-m-d');
            Log::info('command diet-diary-reminder Call.', ['comment' => "diet-diary-reminder service started at :" . $todaysDate]);
            //DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $title = "VLCC - Slimmer's App";
            $extra['title'] = $title;
            $tag = "diet_diary_reminder";
            $result = $this->getMemberDietLogCount($todaysDate);
            $result = json_encode($result);
            $result = json_decode($result, true);

            foreach ($result as $key => $value) {
                $member_id = $result[$key]["member_id"];
                $device_token = $result[$key]["device_token"];
                $device_type = $result[$key]["device_type"];

                if ($result[$key]["diet_log_count"] == 0) {
                    $message_text = "Please update your Diet Diary for today.";
                } else if ($result[$key]["diet_log_count"] >= 1 && $result[$key]["diet_log_count"] <= 4) {
                    $message_text = "Please complete your Diet Diary for today.";
                } else {
                    $message_text = "";
                }
                
                $extra['body'] = $message_text;
                if (!empty($message_text)) {
                    // Insert Data into member_notifications table
                    $recommendation = new $this->model;
                    $allColumns = $recommendation->getTableColumns($recommendation->getTable());
                    $inputs = array("message_type" => "5", "message_text" => $message_text, "status" => "1", "member_id" => $member_id, "created_by" => "1");
                    foreach ($inputs as $key => $value) {
                        if (in_array($key, $allColumns)) {
                            $recommendation->$key = $value;
                        }
                    }
                    $trim = trim($message_text);
                    $recommendation->message_text = ucfirst($trim);
                    $save = $recommendation->save();
                    PushHelper::sendGeneralPushNotification($device_token, $tag, $message_text, $extra, $title, $device_type, $recommendation->id);
                }
            }
            //DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);            
        } catch (Exception $e) {
            Log::info('command diet-diary-reminder Call.', ['comment' => $e->getMessage()]);
        }
    }

    // Function to retreive Members with Diet Log count(daywise) for Diet Diary Reminder Service
    public function getMemberDietLogCount($todaysDate)
    {
        $result = DB::select("SELECT tokens.member_id, tokens.device_token,  tokens.device_type, IFNULL(diet_log.diet_log_count,0) AS diet_log_count FROM member_device_tokens tokens LEFT JOIN
	(
	SELECT member_id, IFNULL(COUNT(DISTINCT(diet_schedule_type_id)), 0) AS diet_log_count
	FROM member_diet_logs WHERE diet_date = '".$todaysDate."'
	GROUP BY member_id
	)  AS diet_log ON
	diet_log.member_id = tokens.member_id LIMIT 1,3");
        return $result;
    }
}
