<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalProceduresRequest;
use App\Http\Resources\LegalProceduresResource;
use App\Models\LegalProcedures;
use Illuminate\Http\Request;

class LegalProceduresController extends Controller
{
    public function __construct(private LegalProcedures $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new LegalProceduresResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', LegalProceduresResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(LegalProceduresRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created');

    }

    public function update($id, LegalProceduresRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->have_children) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->model->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->model->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->model->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }
}
