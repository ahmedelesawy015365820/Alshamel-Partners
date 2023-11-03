<?php

namespace Modules\Archiving\Http\Controllers;

use Database\Seeders\ArchivingCustomTableSeederTableSeeder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Archiving\Entities\ArchivingCustomTable;
use Modules\Archiving\Http\Requests\ArchivingCustomTableRequest;
use Modules\Archiving\Repositories\ArchCustomTable\ArchCustomTableInterface;
use Modules\Archiving\Transformers\ArchivingCustomTableResource;

class ArchivingCustomTableController extends Controller
{
    public function __construct(private ArchCustomTableInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new ArchivingCustomTableResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', ArchivingCustomTableResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ArchivingCustomTableRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new ArchivingCustomTableResource($model));

    }

    public function update(ArchivingCustomTableRequest $request)
    {
        $model = $this->modelInterface->update($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new ArchivingCustomTableResource($model));
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
        $company_id = request()->header('company-id');
        $customTable = ArchivingCustomTable::where([
            ["table_name", $tableName],
            ['company_id', $company_id],
        ])
            ->first();
        if (!$customTable) {
            $customTable = ArchivingCustomTable::where([
                ["table_name", $tableName],
                ['company_id', 0],
            ])->first();
        }
        return $customTable ? $customTable->columns : [];
    }

    public function seeder()
    {

        ArchivingCustomTable::where('company_id', 0)->delete();

        $seeder = new ArchivingCustomTableSeederTableSeeder();
        $seeder->run();

        return responseJson(200, __('Done'));
    }
}
