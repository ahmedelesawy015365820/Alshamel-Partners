<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ClubMembersDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            CmMembersTypesDatabaseSeederTableSeeder::class,
            CmFinancialStatusDatabaseSeederTableSeeder::class,
            CmMembersPermissionsDatabaseSeederTableSeeder::class,
            CmSponsorDatabaseSeederTableSeeder::class,
            CmStatusesTableSeeder::class,
            CmSerialTableSeeder::class,
            CmBranchTableSeeder::class,

        ]);

   
        // // this MUST be separated from above call
        // $this->call([
        //     Artisan::call('member:db'),
        //     Artisan::call('transaction:db'),
        //     Artisan::call('Prefix:db'),
        // ]);



    }
}
