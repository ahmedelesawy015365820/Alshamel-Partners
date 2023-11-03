<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Repositories\RealizedUnrealizedRepository;
use Modules\Stock\Transformers\RealizedUnrealizedResource;


class RealizedUnrealizedController extends Controller
{
    private $modelInterface;
    public function __construct(RealizedUnrealizedRepository $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }
    public function  all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', RealizedUnrealizedResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
