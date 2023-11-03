<?php

namespace App\Http\Controllers\FinancialYear;

use App\Http\Requests\AllRequest;
use App\Http\Requests\FinancialYear\FinancialYearRequest;
use App\Http\Requests\FinancialYear\UpdateFinancialYearRequest;
use App\Http\Resources\FinancialYear\FinancialYearResource;
use App\Repositories\FinancialYear\FinancialYearInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FinancialYearController extends Controller
{
    public function __construct(private FinancialYearInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new FinancialYearResource($model));
    }

    public function all(AllRequest $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('financial-years');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('financial-years', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', FinancialYearResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(FinancialYearRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(FinancialYearRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->have_children) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

    public function DataOfModelFinancialYear(Request $request){
        $model = $this->modelInterface->DataOfModelFinancialYear($request);
        return responseJson(200, 'success', new FinancialYearResource($model));
    }

    // public function addFinancialYearToCompany($module_id, $company_id)
    // {
    //     $model = $this->modelInterface->find($module_id);

    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }

    //     $this->modelInterface->addFinancialYearToCompany($module_id, $company_id);
    //     return responseJson(200, 'success');
    // }

    // public function removeFinancialYearFromCompany($module_id, $company_id)
    // {
    //     $model = $this->modelInterface->find($module_id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }

    //     $this->modelInterface->removeFinancialYearFromCompany($module_id, $company_id);
    //     return responseJson(200, 'success');
    // }

}
