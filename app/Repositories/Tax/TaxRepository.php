<?php


namespace App\Repositories\Tax;

use App\Models\CustomerGroup;
use App\Models\DepertmentTask;
use App\Models\GeneralCustomTable;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Document\DocumentResource;

class TaxRepository implements TaxInterface
{
    public function __construct(private Tax $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

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
        checkIsDefaultGeneral($request['is_default'],$this->model);

        return DB::transaction(function () use ($request) {
           return  $this->model->create($request);
        });
    }


    public function update($request, $id)
    {
        checkIsDefaultGeneral($request['is_default'],$this->model);
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
}
