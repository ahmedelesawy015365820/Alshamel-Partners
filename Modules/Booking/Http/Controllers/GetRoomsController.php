<?php

namespace Modules\Booking\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Unit;
use Modules\Booking\Transformers\UnitResource;

class GetRoomsController extends Controller
{

    public function __construct(private Unit $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->date_from && $request->date_to) {
            $models->where(function ($q) use ($request) {
                $q->whereHas('documentHeaderDetails', function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->whereDate('date_from', '>=', $request->date_from)
                            ->whereDate('date_from', '<=', $request->date_to)->whereRelation('documentHeader', 'document_id', 33);
                    })->orWhere(function ($q) use ($request) {
                        $q->whereDate('date_from', '<=', $request->date_from)
                            ->whereDate('date_to', '>=', $request->date_to)->whereRelation('documentHeader', 'document_id', 33);
                    })->orWhere(function ($q) use ($request) {
                        $q->whereDate('date_to', '>=', $request->date_from)
                            ->whereDate('date_to', '<=', $request->date_to)->whereRelation('documentHeader', 'document_id', 33);
                    });
                });
            });
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', UnitResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }


}
