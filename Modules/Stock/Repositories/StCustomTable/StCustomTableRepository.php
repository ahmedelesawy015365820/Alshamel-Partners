<?php

namespace Modules\Stock\Repositories\StCustomTable;

use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RealestateCustomTable;
use Modules\Stock\Entities\StockCustomTable;

class StCustomTableRepository implements StCustomTableInterface
{

    public function __construct(private StockCustomTable $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        $models_x = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->company_id){
              $x = $models_x->where('company_id',$request->company_id)->pluck('table_name');
                $models->where('company_id',$request->company_id)->orWhere(function ($q) use ($x){
                    $q->where('company_id',0)->whereNotIn('table_name',$x);
                });
        }

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

    public function create($request){
        $company_id = request()->header('company-id');
        $table_name = $request['table_name'];
        $data = $this->model->updateOrCreate(
            [
                'company_id' => $company_id,
                'table_name' => $table_name,
            ],
            [
                'company_id' => $company_id,
                'table_name' => $table_name,
                'columns'    => $request['columns'],
            ]
        );
        return $data;
    }

    public function update($request){
        $company_id = request()->header('company-id');
        $table_name = $request['table_name'];
        $data = $this->model->updateOrCreate(
            [
                'company_id' => $company_id,
                'table_name' => $table_name,
            ],
            [
                'company_id' => $company_id,
                'table_name' => $table_name,
                'columns'    => $request['columns'],
            ]
        );
        return $data;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id){
        $model = $this->model->find($id);
        $model->delete();
    }

}
