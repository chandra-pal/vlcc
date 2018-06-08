<?php

/**
 * The command class for crawling fitnesspal for BMI.
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Console\Commands\SimpleHtmlDom\simple_html_dom;
use App\Console\Commands\SimpleHtmlDom\simple_html_dom_node;
use Modules\Admin\Models\Food;
use Modules\Admin\Models\FoodType;
use Modules\Admin\Repositories\FoodRepository;
use DB;
use Log;
use Exception;

class CrawlFitnessPal extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "crawl:fitnesspal {--search=''}";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $startTime = date('Y-m-d H:i:s');
            $this->comment(PHP_EOL . "crawl:fitnesspal started at :" . $startTime . PHP_EOL);

            $client = new \GuzzleHttp\Client();
            $jar = new \GuzzleHttp\Cookie\CookieJar();
            $customerData = [];
            if (($this->option('search') !== null) && $this->option('search') != '') {
                $searchKey = (string) $this->option('search');
            } else {
                throw new Exception('Search key not provided, please provide search key as --search="<your text>"');
            }



            $url = "http://www.myfitnesspal.com/food/search?search=".urlencode($searchKey);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $html = new simple_html_dom();
            $html->load($server_output);
            $endPage = 0;
            foreach ($html->find('div.pagination a') as $key => $page) {
                if ($key == (count($html->find('div.pagination a')) - 2))
                    $endPage = $page->plaintext;
            }
            $foodArray = [];
            $finalArray = [];
            if ($endPage > 0) {
                for ($i = 1; $i <= $endPage; $i++) {
                    $url1 = "http://www.myfitnesspal.com/food/search?page=" . $i . "&search=" .urlencode($searchKey);
                    $ch1 = curl_init();
                    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch1, CURLOPT_URL, $url1);
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                    $server_output1 = curl_exec($ch1);
                    curl_close($ch1);
                    $html1 = new simple_html_dom ();
                    $html1->load($server_output1);

                    $foodInfoArray = [];
                    foreach ($html1->find('div#new_food_list ul li') as $li) {
                        $foodInfoArray['food_type'] = ucwords($searchKey);
                        $foodInfoArray['food_name'] = $li->find('div.food_info div.food_description a', 0)->plaintext . " (" . $li->find('div.food_info div.food_description a.brand', 0)->plaintext . ")";
                        foreach ($li->find('div.nutritional_info') as $value => $nutriDiv) {
                            $headers = [];
                            array_push($headers, 'food_name');
                            preg_match_all("'</label>(.*?)<label>'si", $nutriDiv->innertext, $r);
                            foreach ($r[1] as $row => $val) {
                                $foodInfoArray[trim($nutriDiv->find('label', $row)->plaintext, ': ')] = str_replace(str_split(',/t/n '), '', $val);
                                array_push($headers, trim($nutriDiv->find('label', $row)->plaintext, ': '));
                            }
                            $this->insertFoods($foodInfoArray);
                            $foodArray[] = $foodInfoArray;
                        }
                    }
                }
            } elseif ($html->find('div#new_food_list ul li')) {
                $foodInfoArray = [];
                foreach ($html->find('div#new_food_list ul li') as $li) {
                    $foodInfoArray['food_type'] = ucwords($searchKey);
                    $foodInfoArray['food_name'] = $li->find('div.food_info div.food_description a', 0)->plaintext . " (" . $li->find('div.food_info div.food_description a.brand', 0)->plaintext . ")";
                    foreach ($li->find('div.nutritional_info') as $value => $nutriDiv) {
                        $headers = [];
                        array_push($headers, 'food_name');
                        preg_match_all("'</label>(.*?)<label>'si", $nutriDiv->innertext, $r);
                        foreach ($r[1] as $row => $val) {
                            $foodInfoArray[trim($nutriDiv->find('label', $row)->plaintext, ': ')] = str_replace(str_split(',/t/n '), '', $val);
                            array_push($headers, trim($nutriDiv->find('label', $row)->plaintext, ': '));
                        }
                        $this->insertFoods($foodInfoArray);
                        $foodArray[] = $foodInfoArray;
                    }
                }
            } else {
                throw new Exception('No Data Found');
            }
            if (!empty($foodArray)) {
                $fileName = public_path('fitnesspal/' . $searchKey . '-food-final.csv');
                $output = fopen($fileName, 'w');
                header("Content-Type:application/csv");
                header("Content-Disposition:attachment;filename=pressurecsv.csv");
                fputcsv($output, $headers);
                foreach ($foodArray as $food) {
                    fputcsv($output, $food);
                }
                fclose($output);
            }

            $endTime = date('Y-m-d H:i:s');
            $this->comment(PHP_EOL . "crawl:fitnesspal ended at :" . $endTime . PHP_EOL);
            Log::info('command crawl:fitnesspal Call.', ['startTime' => $startTime, 'endTime' => $endTime, 'comment' => 'fitnesspal crawled']);
        } catch (Exception $e) {
            $this->comment(PHP_EOL . "crawl:fitnesspal failed with msg :" . $e->getMessage() . PHP_EOL);
            Log::info('command crawl:fitnesspal Call.', ['error_code' => $e->getCode(), 'comment' => $e->getMessage()]);
        }
    }

    public function insertFoods($foods) {
        try {
            if (strlen($foods['food_name']) <= 50) {
                $food = new Food();
                $foodType = FoodType::select('id')->where('food_type_name', 'LIKE', $foods['food_type'])->get();
                $foodTypeId = 0;
                if (!isset($foodType[0]->id)) {
                    $food_type = new FoodType();
                    $food_type->food_type_name = $foods['food_type'];
                    $food_type->created_by = 1;
                    $food_type->save();
                    $foodTypeId = $food_type->id;
                } else {
                    $foodTypeId = $foodType[0]->id;
                }
                $whereCondition = ['food_name' => $foods['food_name'], 'food_type_id' => (int) $foodTypeId];
                $foodArray = [
                    'food_name' => $foods['food_name'],
                    'food_type_id' => (int) $foodTypeId,
                    'measure' => $foods['Serving Size'],
                    'calories' => $foods['Calories'],
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_by_user_type' => 1
                ];
                $save = $food->updateOrCreate($whereCondition, $foodArray);
                if ($save->id) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
