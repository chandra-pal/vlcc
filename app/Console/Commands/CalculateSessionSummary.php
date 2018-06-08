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
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Stream;
use Modules\Admin\Repositories\CPRRepository;
use Modules\Admin\Models\MemberSessionRecordSummary;

class CalculateSessionSummary extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "calculate-session-summary";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time cron to update week day & calculate summary of all uncalculated session programme records';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user, CPRRepository $cprRepository, MemberSessionRecordSummary $memberSessionRecordSummary)
    {
        $this->memberSessionRecordSummary = $memberSessionRecordSummary;
        $this->cprRepository = $cprRepository;

        // Select members to update week_day column
        $members = DB::select("SELECT m1.id, m1.member_id, m1.package_id, m1.recorded_date FROM member_session_record m1 LEFT           JOIN member_session_record m2 ON (m1.member_id = m2.member_id AND m1.id < m2.id) WHERE m2.id IS NULL ORDER BY m1.member_id");
        $members = json_decode(json_encode($members), true);
        foreach ($members as $member) {
            $week_day = date("w", strtotime($member["recorded_date"]));
            $memberData = ['week_day' => $week_day];
            DB::update("UPDATE members SET week_day='" . $week_day . "' WHERE id='" . $member["member_id"] . "'");
        }

        // Calculate summary of all previous records whose summary is not calculated
        $session_programme_records = DB::select("SELECT id, member_id, package_id, session_id FROM member_session_record GROUP BY member_id, package_id ORDER BY member_id, package_id");
        $session_programme_records_result = [];  // multidimensional array
        $session_programme_records = json_decode(json_encode($session_programme_records), true);
        foreach ($session_programme_records as $session_record) {
            $summary_records = DB::select("SELECT id, package_id, member_id, session_id, recorded_date, before_weight, after_weight, created_at, created_by FROM member_session_record WHERE session_id > (SELECT max(session_id) FROM member_session_record_summary WHERE member_id='" . $session_record["member_id"] . "' and package_id='" . $session_record["package_id"] . "') AND member_id='" . $session_record["member_id"] . "' AND package_id='" . $session_record["package_id"] . "' ORDER BY session_id ASC");
            $summary_records = json_decode(json_encode($summary_records), true);
            if (!empty($summary_records)) {
                $session_programme_records_result[] = $summary_records;
            }
            //echo "member id == ".$session_record["member_id"]." package id = ".$session_record["package_id"];
            //echo '<pre>';
            //print_r($summary_records);
        }

        // Calculate summary, insert data into database
        if (!empty($session_programme_records_result)) {

            foreach ($session_programme_records_result as $result) {
                $beforeWeight = $result[0]["before_weight"];
                $afterWeight = $result[count($result) - 1]["after_weight"];
                $package_id = $result[0]["package_id"];
                $member_id = $result[0]["member_id"];
                $session_id = $result[count($result) - 1]["session_id"];
                $created_by = $result[count($result) - 1]["created_by"];
                $recorded_date = date('Y-m-d');

                $weightLoss = 0;
                $weigthGain = 0;

                if ($beforeWeight > $afterWeight) {
                    $weigthGain = 0;
                    $weightLoss = $beforeWeight - $afterWeight;
                } elseif ($beforeWeight < $afterWeight) {
                    $weigthGain = $afterWeight - $beforeWeight;
                    $weightLoss = 0;
                } else {
                    $weigthGain = 0;
                    $weightLoss = 0;
                }

                $lastSummaryRecord = MemberSessionRecordSummary::where('package_id', $package_id)
                    ->where('member_id', $member_id)
                    ->orderBy('recorded_date', 'desc')
                    ->limit(1)
                    ->get();

                $balanceProgrammeKg = 0;
                if (isset($lastSummaryRecord[0])) {
                    $balanceProgrammeKg = ($lastSummaryRecord[0]->balance_programme_kg > $weightLoss) ? $lastSummaryRecord[0]->balance_programme_kg - $weightLoss : 0;
                } else {
                    $packageData = MemberPackage::select('programme_booked')
                        ->where('id', $package_id)
                        ->where('member_id', $member_id)
                        ->get();
                    $balanceProgrammeKg = ($packageData[0]->programme_booked > $weightLoss) ? $packageData[0]->programme_booked - $weightLoss : 0;
                }

                $memberSessionRecordSummary = new $this->memberSessionRecordSummary;

                $memberSessionRecordSummary->package_id = $package_id;
                $memberSessionRecordSummary->member_id = $member_id;
                $memberSessionRecordSummary->session_id = $session_id;
                $memberSessionRecordSummary->recorded_date = $recorded_date;
                $memberSessionRecordSummary->net_weight_loss = $weightLoss;
                $memberSessionRecordSummary->net_weight_gain = $weigthGain;
                $memberSessionRecordSummary->created_by = $created_by;
                $memberSessionRecordSummary->balance_programme_kg = $balanceProgrammeKg;

                $save = $memberSessionRecordSummary->save();

                $inputs = [];
                $inputs["member_id"] = $member_id;
                $inputs["package_id"] = $package_id;
                $inputs["session_id"] = $session_id;
                $inputs["created_by"] = $created_by;

                if (isset($save) && $weightLoss < 1) {
                    $this->info("Session Programme record summary calculated for Member: " . $member_id . " Package: " . $package_id . " Session id: " . $session_id);
                    Log::info("Session Programme record summary calculated for Member: " . $member_id . " Package: " . $package_id . " Session id: " . $session_id);
                    if ($weightLoss < 1) {
                        $this->cprRepository->escalateMember($inputs, $weightLoss, $weigthGain);
                    }
                }
            }
        }
    }
}
