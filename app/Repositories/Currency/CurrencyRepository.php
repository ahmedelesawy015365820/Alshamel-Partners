<?php

namespace App\Repositories\Currency;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public $model;
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }
    public function getAll($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ?$request->order: 'updated_at', $request->sort ?$request->sort: 'DESC');

        if($request->is_default){
            $this->model->whereIsDefault($request->is_default);
        }

        if ($request->per_page){
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model->create($data);
            if (request()->is_default == 1) {
                $this->model->where('id', '!=', $model->id)->update(['is_default' => 0]);
            }
            return $model;

        });
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function update($data, $id)
    {
        DB::transaction(function () use ($id, $data) {
            $this->model->where("id", $id)->update($data);
            if (request()->is_default == 1) {
                $this->model->where('id', '!=', $id)->update(['is_default' => 0]);
            }
        });
    }

    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

  
}
