<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        // Set Seeding to true check if data is seeding.
        // This is required to stop notification in installation
        config(['app.seeding' => true]);
        $this->call(GlobalCurrencySeeder::class);
        $this->call(GlobalSettingTableSeeder::class);
        $this->call(PackageTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(EmailSettingSeeder::class);
        // $this->call(FrontSeeder::class);

        if (!App::environment('codecanyon')) {
            $this->call(ProjectSeeder::class);
        }
        config(['app.seeding' => false]);
    }

}
