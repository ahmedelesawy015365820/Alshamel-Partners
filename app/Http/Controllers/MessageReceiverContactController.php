<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageReceiverContactRequest;
use App\Models\MessageReceiverContact;
use Illuminate\Http\Request;

class MessageReceiverContactController extends Controller
{
    public function __construct(private MessageReceiverContact $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->data()->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', MessageReceiverContact::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(MessageReceiverContactRequest $request)
    {

        foreach ($request->manager_employee_ids as $manager_employee_id) {
            MessageReceiverContact::create([
                'message_request_id' => $request->message_request_id,
                'manager_employee_id' => $manager_employee_id,
            ]);
        }
        return responseJson(200, 'created');

    }

    public function update($id, MessageReceiverContactRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->delete();
        foreach ($request->manager_employee_ids as $manager_employee_id) {
            MessageReceiverContact::create([
                'message_request_id' => $request->message_request_id,
                'manager_employee_id' => $manager_employee_id,
            ]);
        }
        // $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
