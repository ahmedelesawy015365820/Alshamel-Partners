<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Repositories\AllTransactionInterface;
use Modules\Stock\Transformers\AllTransactionResource;

class AllTransactionController extends Controller
{
    private $modelInterface;
    public function __construct(AllTransactionInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }
    public function  all(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', AllTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
