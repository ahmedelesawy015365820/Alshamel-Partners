<?php

namespace App\Repositories\PaymentType;

use Illuminate\Support\Facades\DB;

class PaymentTypeRepository implements PaymentTypeInterface
{

    public function __construct(private \App\Models\PaymentType $model, private \Spatie\MediaLibrary\MediaCollections\Models\Media $media)
    {
        $this->model = $model;
        $this->media = $media;

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
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            if ($request->is_default == 1) {
                $this->model->where('id', '!=', $model->id)->update(['is_default' => 0]);
            }

        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            $model->update($request->all());
            if ($request->is_default == 1) {
                $this->model->where('id', '!=', $id)->update(['is_default' => 0]);
            }

        });

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

}
