<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Archiving\Http\Requests\DocumentFieldRequest;
use Modules\Archiving\Repositories\DocumentFieldInterface;
use Modules\Archiving\Transformers\DocumentFieldResource;

class DocumentFieldController extends Controller
{
    public function __construct(private DocumentFieldInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocumentFieldResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DocumentFieldRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new DocumentFieldResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }

        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, 'data not found');
            }
            if ($model->hasChildren()) {
                return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
            }
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function update(DocumentFieldRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new DocumentFieldResource($model));
    }

    public function getTables()
    {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $data = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'general')) {
                    array_push($data, $value);
                }
            }

        }
        return responseJson(200, 'success', $data);
    }

    public function getColumns($table)
    {
        $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $table);
        $data = [];
        foreach ($columns as $column) {
            array_push($data, $column->Field);
        }
        return responseJson(200, 'success', $data);
    }

    public function getColumnData($table, $column)
    {
        //get columns value
        $data = \Illuminate\Support\Facades\DB::table($table)->select($column)->distinct()->get();
        return responseJson(200, 'success', $data);

    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
