<?php


namespace App\Repositories\ItemBreakDown;

use App\Http\Resources\Document\DocumentResource;
use App\Models\GeneralCustomTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
class ItemBreakDownRepository implements ItemBreakDownInterface
{
    public function __construct(private \App\Models\ItemBreakDown$model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->break_id){
            $models->where('break_id',$request['break_id']);
        }
        if ($request->module_type){
            $models->where('module_type',$request->module_type);
        }
        if ($request->serial_number){
            $models->where('serial_number',$request['serial_number']);
        }
        if ($request->date_to && $request->date_from){
            $models->whereDate('date_to',$request->date_to)
                ->whereDate('date_from',$request->date_from);
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



}
