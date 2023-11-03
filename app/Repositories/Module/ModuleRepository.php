<?php

namespace App\Repositories\Module;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Nwidart\Modules\Facades\Module;

class ModuleRepository implements ModuleInterface
{

    public function __construct(private \App\Models\Module$model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $this->model->create($request->all());
            cacheForget("modules");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
            $this->forget($id);

        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
    }

    // public function addModuleToCompany($module_id, $company_id)
    // {
    //     $this->model->find($module_id);
    //     $this->model->companies()->attach($company_id);
    // }

    // public function removeModuleFromCompany($module_id, $company_id)
    // {
    //     $this->model->find($module_id);
    //     $this->model->companies()->detach($company_id);
    // }
    private function forget($id)
    {
        $keys = [
            "modules",
            "modules_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }


    public function moduleDisable($request)
    {
        collect(Module::all())->each(function ($item , $key){
            Module::enable($key);
        });
        return 250;

//        $request->url()
         // domain Ulr
         $pieces = parse_url($request->url());
         // host addeffect.alshamelalaraby.com
         $host =  $pieces['host'];
         // host array ['addeffect','alshamelalaraby','com']
         $name = explode(".", $host);
         // get name
         $url =  "https://admin.alshamelalaraby.com/api/project-program-modules/all-company-program/".$name[0];


        $request_2 = Http::get($url);
       return $response_2 = $request_2->json();

       if ($response_2){
           $data = [];
           foreach ($response_2 as $index => $response){
               if ($response['name_e'] == 'reservation' ){
                   $data['Booking'] = 'Booking';
               }
           }
           collect(Module::all())->each(function ($item , $key){
               Module::disable($key);
           });

           foreach ($data  as $item){
               Module::enable($item);
           }

           Artisan::call("migrate:fresh");

           return "Success Project Program Modules";

       }

        return "Not Found Date Project Program Modules";
    }

}
