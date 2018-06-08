<?php namespace Modules\Admin\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Log;
use \Carbon\Carbon;
use Cache;

class ExpiredOffers extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "expired:offers";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expired the offers and flush the caches';

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
        //$yestardayDate = Carbon::yesterday()->toDateString();
        $todayDate = Carbon::now()->toDateString();
        $todayDate = $todayDate . ' 00:00:00';
        $tillExpiredDate = Carbon::now()->addMonths(6)->toDateString();
        $offersRecords = DB::table('offers')
            ->where('status', '=', 1)
            ->where('offer_validity_date', '<', $todayDate)
            ->get();
            
        $affected = DB::table('offers')
            ->where('status', '=', 1)
            ->where('offer_validity_date', '<', $todayDate)
            ->update(array('status' => 3, 'expired_at' => $tillExpiredDate));
            
            
        $offersInactiveRecords = DB::table('offers')
            ->where('status', '=', 3)
            ->where('expired_at', '<', $todayDate)
            ->get();
            
        $affectedInactive = DB::table('offers')
            ->where('status', '=', 3)
            ->where('expired_at', '<', $todayDate)
            ->update(array('status' => 0));

        $data = array_merge($offersRecords,$offersInactiveRecords);
        
        $this->flushAssoicatedCacheData($data);

        Log::info('Expired Offers: ', ['till end date' => $todayDate, 'rows affected' => $affected]);
        Log::info('Expired And Inactive Offers: ', ['till date' => $todayDate, 'rows affected' => $affectedInactive]);
        //$this->info('Display this on the screen');
        //$this->error('Something went wrong!');
        $this->comment(PHP_EOL . "Business logic to expired offers." . PHP_EOL);
    }

    /**
     * Flush all cache data
     *
     * @return mixed
     */
    public function flushAssoicatedCacheData($offersRecords)
    {
      if(!empty($offersRecords)) {
        //Cache::tags('offers', 'offers_sub', 'offer_banners')->flush();
        Cache::tags(['offers', 'offers_sub', 'offer_banners'])->flush();
        // Logic to flush offer view page
        foreach ($offersRecords as $offer) {
	    $cacheKey = md5($offer->offer_slug);
	    $offerDataCacheKey = "page-data-{$offer->offer_slug}";
	    $cacheDataKey = md5($offerDataCacheKey);
	    Cache::forget($cacheDataKey);
	    Cache::forget($cacheKey);
        }
	Cache::tags('offers_page')->flush();
      }

    }
}
