<?php

namespace App\Repositories\PaymentMethod;

use Illuminate\Support\Facades\DB;

class PaymentMethodRepository implements PaymentMethodInterface
{

    public function __construct(private \App\Models\PaymentMethod$model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

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
        return DB::transaction(function () use ($request) {
            return $model = $this->model->create($request->all());
            if ($request->is_default == 1) {
                $this->model->where('id', '!=', $model->id)->update(['is_default' => 0]);
            }

        });
    }

    public function update($request, $id)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return null; // or throw an exception
        }
        $model->update($request->all());
        if ($request->is_default == 1) {
            $this->model->where('id', '!=', $id)->update(['is_default' => 0]);
        }
        return $model;

    }
    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }


    public function getName($request)
    {
        $models = $this->model->select('id','name','name_e');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
