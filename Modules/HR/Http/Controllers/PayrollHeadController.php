<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Resources\Log\LogResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\PayrollHead;
use Modules\HR\Http\Requests\PayrollHeadRequest;
use Modules\HR\Transformers\PayrollHeadResource;

class PayrollHeadController extends Controller
{

    public function __construct(private PayrollHead $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new PayrollHeadResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', PayrollHeadResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(PayrollHeadRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new PayrollHeadResource($model));
    }

    public function update($id, PayrollHeadRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new PayrollHeadResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($model->hasChildren()) {
            return responseJson(400, 'has related data');
        }
        $model->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        return DB::transaction(function () {

            $ids = request()->ids;
            if (!$ids) {
                return responseJson(400, 'ids is required');
            }
            $models = $this->model->whereIn('id', $ids)->get();
            if ($models->count() != count($ids)) {
                return responseJson(404, 'not found');
            }
            $models->each(function ($model) {
                if ($model->hasChildren()) {
                    return responseJson(400, $model->name . ' has related data');
                }
                $model->delete();
            });
            return responseJson(200, 'all deleted');
        });
    }


    public function processJsonData(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $maxId = $this->model->max('id') ?? 0;

        $messages = [];
        foreach ($data['data'] as $item) {
            try {
                switch ($item['op']) {
                    case 'ADD':
                        $id = $item['id'] ?? ++$maxId;
                        if ($this->model->where('id', $id)->exists()) {
                            $messages[] = ['id' => $id, 'status' => 'id already exists'];
                        } else {
                            $this->model->insert([
                                'id' => $id,
                                'name' => $item['name'],
                                'name_e' => $item['name_e'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $messages[] = ['id' => $id, 'status' => 'added successfully'];
                        }
                        break;
                    case 'UPD':
                        $id = $item['id'];
                        $model = $this->model->find($id);

                        if ($model) {
                            $this->model->where('id', $id)->update([
                                'name' => $item['name'],
                                'name_e' => $item['name_e'],
                                'updated_at' => now(),
                            ]);
                            $messages[] = ['id' => $id, 'status' => 'updated successfully'];
                        } else {
                            $messages[] = ['id' => $id, 'status' => 'record not found'];
                        }
                        break;
                    case 'DEL':
                        $id = $item['id'];
                        $model = $this->model->find($item['id']);
                        if ($model) {

                            $model->delete();
                            $messages[] = ['id' => $id, 'status' => 'deleted successfully'];
                        } else {
                            $messages[] = ['id' => $id, 'status' => 'record not found'];
                        }
                        break;
                }
            } catch (\Exception $e) {
                $messages[] = ['id' => $item['id'], 'status' => $e->getMessage()];
            }
        }

        return response()->json($messages);
    }
}
