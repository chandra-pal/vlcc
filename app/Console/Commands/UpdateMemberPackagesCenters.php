<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\MemberSessionRecord;
use Modules\Admin\Models\SessionBookings;
use DB;
use Log;

class UpdateMemberPackagesCenters extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-member-packages-centers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service to update member packages with centers';

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
            $startTime = date('Y-m-d H:i:s');
            Log::info('command update-member-packages-centers Call.', ['comment' => "update member packages centers started at :" . $startTime]);
            $result = DB::select('select count(*) as cnt, member_packages.member_id, members.crm_center_id from member_packages 
left outer join members ON member_packages.member_id = members.id group by member_id');
            $result = json_decode(json_encode($result), true);

            foreach ($result as $key) {
                $member_package = new MemberPackage();
                $member_session_booking = new SessionBookings();
                $member_session_record = new MemberSessionRecord();
                $memberData = ['status' => 1, 'crm_center_id' => $key['crm_center_id']];
                $crmCenterId = ['crm_center_id' => $key['crm_center_id']];
                $save = $member_package->where('member_id', $key["member_id"])->update($memberData);
                $save1 = $member_session_booking->where('member_id', $key["member_id"])->update($crmCenterId);
                $save2 = $member_session_record->where('member_id', $key["member_id"])->update($crmCenterId);
            }
            
            $endTime = date('Y-m-d H:i:s');
            Log::info('command update-member-packages-centers Call.', ['comment' => "update member packages centers service ended at :" . $endTime]);
        } catch (Exception $e) {
            Log::info('command update-member-packages-centers Call.', ['comment' => $e->getMessage()]);
        }
    }
}
