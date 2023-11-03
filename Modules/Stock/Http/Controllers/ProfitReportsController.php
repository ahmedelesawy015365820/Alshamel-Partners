<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Repositories\ProfitReportInterface;
use Modules\Stock\Transformers\ProfitReportsResource;

class ProfitReportsController extends Controller
{
    private $modelInterface;
    public function __construct(ProfitReportInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }
    public function  all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', ProfitReportsResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
