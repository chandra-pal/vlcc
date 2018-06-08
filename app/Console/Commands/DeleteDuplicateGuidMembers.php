<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class DeleteDuplicateGuidMembers extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-duplicate-guid-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to delete duplicate members';

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
            $result = DB::select('SELECT count(*) as cnt, crm_member_guid FROM members group by crm_member_guid having cnt > 1');
            $result = json_decode(json_encode($result), true);
            $result = array_column($result, 'crm_member_guid');

            foreach ($result as $key) {
                $memberIds = array();
                if(empty($key)) {
                    $members = DB::select("SELECT id FROM members WHERE crm_member_guid is null");
                } else {
                    $members = DB::select("SELECT id FROM members WHERE crm_member_guid='" . $key . "'");
                }
                $members = json_decode(json_encode($members), true);
                $memberIds = array_column($members, 'id');
                $this->deleteMemberData($memberIds, $key);
            }
        } catch (Exception $e) {
            Log::info('command delete-duplicate-members Call.', ['comment' => $e->getMessage()]);
        }
    }

    // Function to retreive Members with Diet Log count(daywise) for Diet Diary Reminder Service
    public function deleteMemberData($memberIds, $member_guid)
    {
        $file_name = date('Y-M-d');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $comma_separated_ids = implode(",", $memberIds);

        DB::table('member_skin_hair_analysis')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_session_record_summary')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_session_record')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_session_booking_resources')->whereIn('member_id', $memberIds)->delete();
        
        DB::table('member_reminders')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_product_recommendations')->whereIn('member_id', $memberIds)->delete();
        
        DB::table('member_package_images')->whereIn('member_id', $memberIds)->delete();
        
        DB::table('member_offers_recommendations')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_notifications')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_medical_review')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_medical_assessment')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_measurement_records')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_measurement_details')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_fitness_assessment')->whereIn('member_id', $memberIds)->delete();

        DB::table('member_fintness_activity_review')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_escalation_matrix')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_diet_recommendations')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_diet_plan_details')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_diet_logs')->whereIn('member_id', $memberIds)->delete();


        DB::table('member_diet_deviations')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_dietary_assessment')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_device_tokens')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_biochemical_profile')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_bca_details')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_activity_recommendation')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_activity_logs')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_activity_deviation')->whereIn('member_id', $memberIds)->delete();
        
        DB::table('member_session_bookings')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_package_services')->whereIn('member_id', $memberIds)->delete();
        DB::table('member_packages')->whereIn('member_id', $memberIds)->delete();
        DB::table('members')->whereIn('id', $memberIds)->delete();
        
        DB::table('foods')->whereIn('created_by', $memberIds)->where('created_by_user_type', 0)->delete();

        $file = fopen("/var/www/html/vlcc-admin/storage/logs/duplidate_guid_members/" . $file_name . ".log", "a");
        $data = "\n"."***** Member Guid : $member_guid"."\n";
        $data = $data."******************************* Member Ids ******************************************" . "\n";
        $data = $data . $comma_separated_ids;
        fwrite($file, $data);
        fclose($file);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
}
