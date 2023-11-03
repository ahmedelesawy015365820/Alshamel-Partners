<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\TimeTableType;
use Modules\HR\Transformers\TimeTableTypeResource;

class TimeTableTypeController extends Controller
{
    public function __construct(private TimeTableType $model)
    {
        $this->model = $model;
    }


    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TimeTableTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
