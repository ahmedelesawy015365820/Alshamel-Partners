<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Transformers\RlstUnSoldUnitReportResource;

class RlstUnSoldUnitReportController extends Controller
{

    public function __construct(private RlstUnit $unitModel)
    {
        $this->unitModel = $unitModel;

    }
    public function all(Request $request)
    {

        $models = $this->unitModel->filter($request)->unsoldfilter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');


        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstUnSoldUnitReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
