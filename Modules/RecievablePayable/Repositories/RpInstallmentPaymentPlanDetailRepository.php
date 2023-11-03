<?php

namespace Modules\RecievablePayable\Repositories;

use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\PropertyType;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentPlanDetail;

class RpInstallmentPaymentPlanDetailRepository implements RpInstallmentPaymentPlanDetailRepositoryInterface
{

    private $model;
    private $setting;
    private $modelPlan;

    public function __construct(RpInstallmentPaymentPlanDetail $model, UserSettingScreen $setting ,RpInstallmentPaymentPlan $modelPlan)
    {
        $this->model = $model;
        $this->setting = $setting;
        $this->modelPlan = $modelPlan;
    }

    public function allPlan($request)
    {
        $models = $this->modelPlan->withCount('installment_payment_plan_details as count_installment_payment_Plan_Detail')->where(function ($q) use ($request) {
            $this->modelPlan->scopeFilter($q , $request);
        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function all($request)
    {
        $models = $this->model->where(function ($q) use ($request) {
            $this->model->scopeFilter($q , $request);
        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findPlan($id)
    {
        return $this->modelPlan->find($id);
    }


    public function create($request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->installment_payment_plan_details as  $details):
                $model = $this->model->create($details);
            endforeach;
            cacheForget("RpInstallmentPaymentPlanDetail");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->where('installment_payment_plan_id',$id)->get();
            foreach ($model as $value):
                $value->delete();
            endforeach;
            foreach ($request->installment_payment_plan_details as  $details):
                $model = $this->model->create($details);
            endforeach;
        });

    }

    public function delete($id)
    {
        $model = $this->modelPlan->find($id);
        foreach ( $model->installment_payment_plan_details as $value):
            $value->delete();
        endforeach;
    }


    public function setting($request)
    {

        DB::transaction(function () use ($request) {

            $model = $this->setting->where('user_id', $request['user_id'])->where('screen_id', $request['screen_id'])->first();

            $request['data_json'] = json_encode($request['data_json']);

            if (!$model) {
                $this->setting->create($request->all());
            } else {
                $model->update($request->all());
            }

        });
    }

    public function getSetting($user_id, $screen_id)
    {
        return $this->setting->where('user_id', $user_id)->where('screen_id', $screen_id)->first();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    private function forget($id)
    {
        $keys = [
            "RpInstallmentPaymentPlanDetail",
            "RpInstallmentPaymentPlanDetail_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

}
