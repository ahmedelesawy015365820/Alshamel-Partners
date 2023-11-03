<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;;
use Illuminate\Http\Request;
use Modules\Stock\Http\Requests\StockSectorRequest;
use Modules\Stock\Repositories\StockSectorInterface;
use Modules\Stock\Transformers\StockSectorResource;

class StockSectorController extends Controller
{
    private $modelInterface;
    public function __construct(StockSectorInterface $modelInterface) {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('stockSector');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('stockSector', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', StockSectorResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StockSectorRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function update(StockSectorRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        return responseJson(200, 'success');
    }
}
