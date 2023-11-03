<?php

namespace App\Repositories\Depertment;

use Illuminate\Support\Facades\DB;

class DepertmentRepository implements DepertmentInterface
{

    public function __construct(private \App\Models\Depertment $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employees) {
            $models->whereHas('employees');
        } //للحذف عند النهايه

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        $data = $this->model->find($id);
        return $data;
    }

    public function create($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $this->model->create($request);
            return $data;
        });
    }

    public function update($request, $id)
    {
        $data = $this->model->find($id);
        $data->update($request);
        return $data;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        $model->delete();
    }

    public function processJsonData($data)
    {
        $maxId = $this->model->max('id') ?? 0;

        $messages = [];
        foreach ($data['data'] as $item) {
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
                            'supervisors' => json_encode($item['supervisor_id'] ?? null),
                            'attentions' => json_encode($item['attention_id'] ?? null),
                            'company_id' => $item['company_id'] ?? null,
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
                            'supervisors' => json_encode($item['supervisor_id'] ?? null),
                            'attentions' => json_encode($item['attention_id'] ?? null),
                            'company_id' => $item['company_id'] ?? null,
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
        }

        return $messages;
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->employees) {
            $models->whereHas('employees');
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
