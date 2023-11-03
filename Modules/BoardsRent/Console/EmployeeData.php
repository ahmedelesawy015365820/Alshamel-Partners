<?php

namespace Modules\BoardsRent\Console;

use App\Models\City;
use App\Models\Employee;
use App\Models\Governorate;
use App\Models\Street;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'board-rent:employee-data';

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
        $this->info('Start Employee Data');
        Schema::dropIfExists('admins');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/admins.sql'));
        DB::unprepared($sql);

        Schema::dropIfExists('model_has_roles');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/model_has_roles.sql'));
        DB::unprepared($sql);

        $ids = DB::table('model_has_roles')->select('model_id')->where('role_id', 2)->pluck('model_id')->toArray();
        $admins = DB::table('admins')->whereIn('id', $ids)->get();
        foreach ($admins as $admin) {
            \App\Models\Employee::create([
                'name' => $admin->name,
                'name_e' => $admin->name,
                'email' => $admin->email,
                'mobile' => $admin->mobile,
                'whatsapp' => $admin->mobile,
            ]);
        }


        Schema::dropIfExists('admins');

        Schema::dropIfExists('model_has_roles');

        $this->info('End Employee Data');
    }
}
