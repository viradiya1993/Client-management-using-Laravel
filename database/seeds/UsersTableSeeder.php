<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        \DB::table('users')->delete();
        \DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        $faker = \Faker\Factory::create();
        $companies = \App\Company::all();
        $firstCompanyID = $companies->first()->id;
        $user = new User();
        $user->name = 'Admin Name';
        $user->email = 'admin@example.com';
        $user->password = Hash::make('123456');
        $user->company_id = $firstCompanyID;
        $user->save();

        $employee = new \App\EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = 'emp-'.$user->id;
        $employee->company_id = $firstCompanyID;
        $employee->address = 'address';
        $employee->hourly_rate = '50';
        $employee->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $user->id;
        $search->company_id = $firstCompanyID;
        $search->title = $user->name;
        $search->route_name = 'admin.employees.show';
        $search->module_type = 'employee';
        $search->save();

        $companies->each(function ($company) use ( $faker) {
            $user = new User();
            $user->name = $faker->name;
            $user->email = $faker->safeEmail;
            $user->password = Hash::make('123456');
            $user->company_id = $company->id;
            $user->save();

            $employee = new \App\EmployeeDetails();
            $employee->user_id = $user->id;
            $employee->employee_id = 'emp-'.$user->id;
            $employee->company_id = $company->id;
            $employee->address = 'address';
            $employee->hourly_rate = '50';
            $employee->save();

            $search = new \App\UniversalSearch();
            $search->searchable_id = $user->id;
            $search->company_id = $company->id;
            $search->title = $user->name;
            $search->route_name = 'admin.employees.show';
            $search->module_type = 'employee';
            $search->save();
        });



        if (!App::environment('codecanyon')) {
            // Employee details
            $user = new User();
            $user->name = 'Employee Name';
            $user->email = 'employee@example.com';
            $user->company_id = $firstCompanyID;
            $user->password = Hash::make('123456');
            $user->save();

            $search = new \App\UniversalSearch();
            $search->searchable_id = $user->id;
            $search->company_id = $firstCompanyID;
            $search->title = $user->name;
            $search->route_name = 'admin.employees.show';
            $search->module_type = 'employee';
            $search->save();

            $employee = new \App\EmployeeDetails();
            $employee->user_id = $user->id;
            $employee->employee_id = 'emp-'.$user->id;
            $employee->company_id = $firstCompanyID;
            $employee->address = 'address';
            $employee->hourly_rate = '25';
            $employee->save();

            // Client details
            $user = new User();
            $user->name = 'Client Name';
            $user->company_id =  $firstCompanyID;
            $user->email = 'client@example.com';
            $user->password = Hash::make('123456');
            $user->save();

            $client = new \App\ClientDetails();
            $client->user_id = $user->id;
            $client->company_id = $firstCompanyID;
            $client->name = $user->name;
            $client->email = $user->email;
            $client->mobile = $user->mobile;
            $client->image = $user->image;
            $client->company_name = 'Company Name';
            $client->address = 'Company address';
            $client->website = 'www.domain-name.com';
            $client->save();

           // factory(User::class, 50)->create();
        }

        $user = new User();
        $user->name = 'Super Admin';
        $user->email = 'superadmin@example.com';
        $user->password = Hash::make('123456');
        $user->super_admin = '1';
        $user->save();
    }

}
