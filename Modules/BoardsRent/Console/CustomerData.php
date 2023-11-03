<?php

namespace Modules\BoardsRent\Console;

use App\Models\City;
use App\Models\Employee;
use App\Models\Governorate;
use App\Models\Street;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\BoardsRent\Entities\Customer;

class CustomerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'board-rent:customer-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        $this->info('Start clients Data');
        Schema::dropIfExists('clients');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/clients.sql'));
        DB::unprepared($sql);

        Schema::dropIfExists('locations');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/locations.sql'));
        DB::unprepared($sql);

        $clients =    DB::table('clients')->get();

        foreach ($clients as $client) {
            $customer = Customer::create([
                'name' => $client->name,
                'name_e' => $client->name,
                'phone' => $client->mobile,
                'email' => $client->email,
                'twitter' => $client->twitter,
                'website' => $client->website,
                'mobile' => $client->mobile,
                'facebook' => $client->facebook,
                'instagram' => $client->instgram,
                'snapchat' => $client->snapchat,
                "sector_id" => $client->sector_id
            ]);

            $location = DB::table('locations')->where('id', $client->location_id)->first();
            if ($location) {
                if ($location->parent_id) {
                    $name = json_decode($location->name);
                    $city = City::where('name', $name->en)->first();
                    if ($city) {
                        $customer->update([
                            'city_id' => $city->id,
                        ]);
                    }

                    $parent_location =
                        DB::table('locations')->where('id', $location->parent_id)->first();
                    $name = json_decode($parent_location->name);
                    $governorate = Governorate::where('name', $name->en)->first();
                    if ($governorate) {
                        $customer->update([
                            'governorate_id' => $governorate->id,
                        ]);
                    }
                } else {
                    $name = json_decode($location->name);
                    $city = City::where('name', $name->en)->first();
                    if ($city) {
                        $customer->update([
                            'city_id' => $city->id,
                        ]);
                    }
                }
            }
        }


        Schema::dropIfExists('locations');
        Schema::dropIfExists('clients');



        $this->info('End clients Data');
    }
}
