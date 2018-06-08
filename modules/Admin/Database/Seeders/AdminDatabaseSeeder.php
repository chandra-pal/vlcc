<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //disable foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call('Modules\Admin\Database\Seeders\AdminsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\IpAddressesTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\UserLinksTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\MenuGroupsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\LinkCategoriesTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\UserTypeTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\LinksTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\UserTypeLinksTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\CountryTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\StateTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\CityTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\LocationsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\ConfigCategoryTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\ConfigSettingsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\SystemEmailsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\OauthClientsTableSeeder');

        $this->call('Modules\Admin\Database\Seeders\FoodTypeTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\FoodsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\MeasurementsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\ReminderTypesTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\DietScheduleTypesTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\ActivityTypesTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\DietPlanDetailsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\MemberDietPlanDetailsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\TrimmedDietPlanDetailsTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\TrimmedVlccCentersTableSeeder');

        $this->call('Modules\Admin\Database\Seeders\BiochemicalConditionTableSeeder');
        $this->call('Modules\Admin\Database\Seeders\BiochemicalConditionTestTableSeeder');

        $this->call('Modules\Admin\Database\Seeders\TemporaryTableSeeder');  // Temporary Seeders
        //enable foreign key check for this connection after running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Model::reguard();
    }
}
