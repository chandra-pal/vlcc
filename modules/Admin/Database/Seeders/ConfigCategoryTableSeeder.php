<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;
use Modules\Admin\Models\ConfigCategory;

class ConfigCategoryTableSeeder extends Seeder
{

    public function __construct(ConfigCategory $configCategory)
    {
        $this->model = $configCategory;
    }

    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('config_categories')->truncate();

        $categories = [ 'General Site Settings', 'Email Settings', 'Phone Settings', 'Pagination Settings', 'Date Settings', 'File Uploading Settings', 'App Specific Categories'];

        //Create config category, id of 1
        foreach ($categories as $cat_name) {
            $configCategory = new $this->model;

            $configCategory->category = $cat_name;
            $configCategory->created_by = '1';
            $configCategory->created_at = Carbon::now();
            $configCategory->updated_by = '1';
            $configCategory->updated_at = Carbon::now();

            $configCategory->save();
        }


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
