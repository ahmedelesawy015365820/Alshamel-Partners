<?php


namespace App\Repositories\BreakSettlement;

use App\Http\Resources\Document\DocumentResource;
use App\Models\GeneralCustomTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
class BreakSettlementRepository implements BreakSettlementInterface
{
    public function __construct(private \App\Models\BreakSettlement$model)
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
        return $this->model->data()->find($id);
    }

    public function create($request)
    {

       return DB::transaction(function () use ($request) {
           $data = [];
           foreach ($request['break_settlement'] as $settlement ){
               $data[] = $this->model->create($settlement);
           }
            return $data;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request);

        });

        $model = $this->model->find($id);
        return $model;
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
