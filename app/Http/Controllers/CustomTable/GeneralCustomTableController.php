<?php

namespace App\Http\Controllers\CustomTable;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomTableRequest;
use App\Http\Resources\CustomTable\CustomTableResource;
use App\Models\GeneralCustomTable;
use Database\Seeders\CustomTableSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class GeneralCustomTableController extends Controller
{

    public function __construct(private \App\Repositories\CustomTable\CustomTableInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CustomTableResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', CustomTableResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(CustomTableRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new CustomTableResource($model));

    }

    public function update(CustomTableRequest $request)
    {
        $model = $this->modelInterface->update($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new CustomTableResource($model));
    }
    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function getCustomTableFields($tableName)
    {

        $allColumns = Schema::getColumnListing($tableName);
        $currentColumnsCount = count(Schema::getColumnListing($tableName));

         

        $company_id = request()->header('company-id');

        $CompanyIdExists = true;
        $CompanyIdValue = 0;
        
        if (Schema::hasColumn($tableName, 'company_id')) {
            // The `company_id` column exists in the `$tableName` table
            $customTable = GeneralCustomTable::where('table_name', $tableName)
        ->where(function ($query) use ($company_id) {
            $query->where('company_id', null)
                ->orWhere('company_id', $company_id)
                ->orWhere('company_id', 0);
        })->first();

        $CompanyIdValue = $customTable['company_id'];
        
        } else {
            
            $CompanyIdExists = false;
            // The `company_id` column does not exist in the `$tableName` table
            $customTable = GeneralCustomTable::where("table_name", $tableName)->first();
        }

        //return $customTable['columns'];

    

        $recordedColumnsCount = count($customTable['columns']);


        if ($currentColumnsCount != $recordedColumnsCount) {
            
            $newColumnsArray = [];
            $i =0;

            $collection = new Collection(json_decode($customTable, true)['columns']);


            foreach($allColumns as $column){

                $columnFound = $collection->first(function ($value, $key) use ($column){
                    return $value['column_name'] === $column;
                });

                if ($columnFound) {
                    // The `column_name` value `"name"` was found in the JSON
                    // The `$column` variable contains the matching column object

                    $newColumnsArray[$i]['column_name'] = $column;
                    $newColumnsArray[$i]['is_required'] = $columnFound['is_required'];
                    $newColumnsArray[$i]['is_visible'] = $columnFound['is_visible'];

                } else {
                    // The `column_name` value `"name"` was not found in the JSON
                    $newColumnsArray[$i]['column_name'] = $column;
                    $newColumnsArray[$i]['is_required'] = 0;
                    $newColumnsArray[$i]['is_visible'] = 1;
                }              

                $i++;
            }

            $customTable['columns'] = $newColumnsArray;

        }
 
        return $customTable ? $customTable->columns : [];

    }

    public function seeder()
    {

        GeneralCustomTable::where('company_id', 0)->delete();

        $seeder = new CustomTableSeeder();
        $seeder->run();

        return responseJson(200, __('Done'));
    }
}
