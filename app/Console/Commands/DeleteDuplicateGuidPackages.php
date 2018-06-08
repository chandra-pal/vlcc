<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class DeleteDuplicateGuidPackages extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-duplicate-guid-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to delete duplicate packages';

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
            /* $file = fopen("/var/www/html/vlcc-admin/storage/logs/clm_package_insertion_request_files/" . $file_name . ".log", "a");
              $data = "\n\n" . $memberPackages["PackageHeaderID"] . "@" . $memberPackages["CRM_MEMBER_GUID"] . "\n";
              $data = $data . "******************************* Request ******************************************" . "\n";
              $data = $data . json_encode($memberPackages);
              fwrite($file, $data);
              fclose($file); */

            //$result = DB::select('SELECT count(*) as cnt, crm_member_guid FROM members where crm_member_guid is NOT null group by crm_member_guid having cnt > 1');
            $result = DB::select('SELECT count(*) as cnt, crm_package_guid FROM member_packages  group by crm_package_guid having cnt > 1');
            $result = json_decode(json_encode($result), true);
            $result = array_column($result, 'crm_package_guid');

            foreach ($result as $key) {
                $memberIds = array();
                if (empty($key)) {
                    $members = DB::select("SELECT id FROM member_packages WHERE crm_package_guid is null");
                } else {
                    $members = DB::select("SELECT id FROM member_packages WHERE crm_package_guid='" . $key . "'");
                }
                $members = json_decode(json_encode($members), true);
                $memberIds = array_column($members, 'id');
                $this->deleteMemberData($memberIds, $key);
            }
        } catch (Exception $e) {
            Log::info('command delete-duplicate-guid-packages Call.', ['comment' => $e->getMessage()]);
        }
    }

    // Function to retreive Members with Diet Log count(daywise) for Diet Diary Reminder Service
    public function deleteMemberData($memberIds, $member_guid)
    {
        $file_name = date('Y-M-d');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $comma_separated_ids = implode(",", $memberIds);

        DB::table('member_session_record_summary')->whereIn('package_id', $memberIds)->delete();
        DB::table('member_session_record')->whereIn('package_id', $memberIds)->delete();

        DB::table('member_package_services')->whereIn('package_id', $memberIds)->delete();
        DB::table('member_package_images')->whereIn('package_id', $memberIds)->delete();
        DB::table('member_escalation_matrix')->whereIn('package_id', $memberIds)->delete();
        DB::table('member_measurement_details')->whereIn('package_id', $memberIds)->delete();

        $sessions = DB::select('SELECT id FROM member_session_bookings WHERE package_id IN('.$comma_separated_ids.')');
        $sessions = json_decode(json_encode($sessions), true);
        $sessions = array_column($sessions, 'id');
        
        
        //$comma_separated_session_ids = implode(",", $sessions);        
        
        DB::table('member_session_booking_resources')->whereIn('session_id', $sessions)->delete();
        DB::table('member_session_bookings')->whereIn('package_id', $memberIds)->delete();

        DB::table('member_bca_details')->whereIn('package_id', $memberIds)->delete();
        DB::table('member_packages')->whereIn('id', $memberIds)->delete();


        $file = fopen("/var/www/html/vlcc-admin/storage/logs/duplidate_guid_packages/" . $file_name . ".log", "a");
        $data = "\n" . "***** Package Guid : $member_guid" . "\n";
        $data = $data . "******************************* Package Ids ******************************************" . "\n";
        $data = $data . $comma_separated_ids;
        fwrite($file, $data);
        fclose($file);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
}
