<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\StatuseSeeder;
use Illuminate\Support\Facades\Artisan;
use Modules\ClubMembers\Database\Seeders\StatusSeederTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $this->call([
            CountrySeeder::class,
            TranslationSeeder::class,
            DataTypeSeeder::class,
            // UserSeeder::class,
            GeneralCustomerSeeder::class,
            NewCustomerSeeder::class,
            CustomTableSeeder::class,
            RpInstallmentPaymentTypeSeeder::class,
            RpInstallmentStatusSeeder::class,
            PaymentMethodSeeder::class,
            // DataTypeSeeder::class,
            // UserSeeder::class
            // ArchiveFileSeeder::class
            RlstUnitStatuesTableSeeder::class,
            RestartPeriodTableSeeder::class,
            // SalesmenPlansSeeder::class,
            StatuseSeeder::class,
            RestartPeriodTableSeeder::class,
            DocumentStatuseSeeder::class,
            StatusSeederTableSeeder::class,
            HrStatusSeeder::class,
            BoardRentSellMethodSeeder::class,
            BranchSeeder::class,
            HrRequestsTypeSeeder::class,
            // PermissionTableSeeder::class,
            RealestateCustomTableSeederTableSeeder::class,
            ClubMembersCustomTableSeederTableSeeder::class,
            RecievablePayableCustomTableSeederTableSeeder::class,
            ArchivingCustomTableSeederTableSeeder::class,
            BoardsRentCustomTableSeederTableSeeder::class,
            CustodyCustomTableSeederTableSeeder::class,
            StockCustomTableSeederTableSeeder::class,
            HRCustomTableSeederTableSeeder::class,
            BookingUnitStatuesTableSeeder::class,
            BookingSettingTableSeeder::class,
            HrTimeTableTypesSeeder::class,
            HrAttendanceSettingSeeder::class,
            MessageVarSeeder::class,
            ClientTypeSeeder::class,
            DocumentModuleTypeSeeder::class,

        ]);
        DB::unprepared(file_get_contents(public_path("country_lookup.sql")));
        // seed all modules
        // Artisan::call('module:seed archiving');
        // Artisan::call('module:seed boardsrent');
        // Artisan::call('module:seed clubmembers');
        // Artisan::call('module:seed custody');
        // Artisan::call('module:seed gl');
        // Artisan::call('module:seed hr');
        // Artisan::call('module:seed realestate');
        // Artisan::call('module:seed recievablepayable');
        // Artisan::call('module:seed stock');
    }
}
