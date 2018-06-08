<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class DeleteDuplicateGuidServices extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-duplicate-guid-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to delete duplicate services';

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
    public function handle()
    {
        try {
            $result = DB::select('SELECT count(*) as cnt, crm_service_guid FROM member_package_services group by crm_service_guid having cnt > 1');
            $result = json_decode(json_encode($result), true);
            $result = array_column($result, 'crm_service_guid');

            foreach ($result as $key) {
                $memberIds = array();
                if (empty($key)) {
                    $members = DB::select("SELECT id FROM member_package_services WHERE crm_service_guid is null");
                } else {
                    $members = DB::select("SELECT id FROM member_package_services WHERE crm_service_guid='" . $key . "'");
                }
                $members = json_decode(json_encode($members), true);
                $memberIds = array_column($members, 'id');
                $this->deleteMemberData($memberIds, $key);
            }
            dd("done");
        } catch (Exception $e) {
            Log::info('command delete-duplicate-guid-services Call.', ['comment' => $e->getMessage()]);
        }
    }

    // Function to retreive Members with Diet Log count(daywise) for Diet Diary Reminder Service
    public function deleteMemberData($memberIds, $member_guid)
    {
        $file_name = date('Y-M-d');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $comma_separated_ids = implode(",", $memberIds);
        $session_ids = array();

        foreach ($memberIds as $service_id) {
            $sessions = DB::select("select id from member_session_bookings where FIND_IN_SET('" . $service_id . "', service_id) ");
            if (!empty($sessions)) {
                $sessions = json_decode(json_encode($sessions), true);
                $sessions = array_column($sessions, 'id');
                $session_ids = array_merge($session_ids, $sessions);
            }
        }

        if (!empty($session_ids)) {
            DB::table('member_escalation_matrix')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_measurement_details')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_measurement_records')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_session_booking_resources')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_session_record_summary')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_session_record')->whereIn('session_id', $session_ids)->delete();
            DB::table('member_session_bookings')->whereIn('id', $session_ids)->delete();
        }

        DB::table('member_package_services')->whereIn('id', $memberIds)->delete();

        $file = fopen("/var/www/html/vlcc-admin/storage/logs/duplidate_guid_packages/" . $file_name . ".log", "a");
        $data = "\n" . "***** Service Guid : $member_guid" . "\n";
        $data = $data . "******************************* Service Ids ******************************************" . "\n";
        $data = $data . $comma_separated_ids;
        fwrite($file, $data);
        fclose($file);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
}
