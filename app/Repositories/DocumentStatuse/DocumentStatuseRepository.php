<?php


namespace App\Repositories\DocumentStatuse;

use App\Http\Resources\Document\DocumentResource;
use App\Models\GeneralCustomTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
class DocumentStatuseRepository implements DocumentStatuseInterface
{
    public function __construct(private \App\Models\DocumentStatuse$model)
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


    public function create($request){
        return DB::transaction(function () use ($request) {
            $data = $this->model->create($request);
            return $data;
        });
    }


    public function update($request,$id){
        $data = $this->model->find($id);
        $data->update($request);
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
