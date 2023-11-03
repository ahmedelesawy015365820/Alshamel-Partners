<?php

namespace App\Http\Controllers\ItemBreakDown;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepertmentRequest;
use App\Http\Resources\Depertment\DepertmentResource;
use App\Http\Resources\ItemBreakDown\ItemBreakDownResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemBreakDownController extends Controller
{
    public function __construct(private \App\Repositories\ItemBreakDown\ItemBreakDownInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new ItemBreakDownResource($model));
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', ItemBreakDownResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }






}
