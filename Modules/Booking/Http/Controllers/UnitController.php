<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Entities\Unit;
use Modules\Booking\Http\Requests\UnitRequest;
use Modules\Booking\Transformers\UnitResource;

class UnitController extends Controller
{
    public function __construct(private Unit $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new UnitResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->unit_status_id) {
            $models->where('unit_status_id', $request->unit_status_id);
        }

        if ($request->start_date && $request->end_date) {

            $models = DB::table('booking_units')
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->select('unit_status_id', DB::raw('count(*) as total'))
                ->groupBy('unit_status_id');

            if ($request->per_page) {
                $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
            } else {
                $models = ['data' => $models->get(), 'paginate' => false];
            }

            return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);

        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', UnitResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(UnitRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();
        return responseJson(200, 'created', new UnitResource($model));

    }

    public function update($id, UnitRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new UnitResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->hasChildren()){
            return responseJson(404, __('message. not Delete'));

        }

        $model->delete();

        return responseJson(200, 'success');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            if (!$model->hasChildren()){
                $model->delete();
            }
        });
        return responseJson(200, 'deleted');
    }

    public function getDropDown(Request $request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getClientUnits(Request $request)
    {
        $documentIgnore = [33,34];

        $models = $this->model->whereHas('documentHeaderDetails',function ($q) use ($request,$documentIgnore){
            $q->where('unit_type','Booking')->whereHas('documentHeader',function ($q) use ($request,$documentIgnore){
                $q->where('customer_id',$request->client_id)
                    ->where('complete_status','UnDelivered')
                    ->whereNotIn('document_id',$documentIgnore);
            });

        })->select('id', 'name', 'name_e');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

}
