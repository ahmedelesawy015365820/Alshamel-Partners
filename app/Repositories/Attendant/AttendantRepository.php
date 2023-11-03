<?php

namespace App\Repositories\Attendant;

use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;

class AttendantRepository implements AttendantInterface
{

    public function __construct(private \App\Models\Attendant $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model
        ->data()
        ->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->customer_id)
        {
            $models->where('customer_id',$request->customer_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            return $model;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }



}
