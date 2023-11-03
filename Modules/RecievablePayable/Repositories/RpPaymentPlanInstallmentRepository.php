<?php

namespace Modules\RecievablePayable\Repositories;

use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\PropertyType;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentPlanDetail;
use Modules\RecievablePayable\Entities\RpPaymentPlanInstallment;

class RpPaymentPlanInstallmentRepository implements RpPaymentPlanInstallmentRepositoryInterface
{

    private $model;
    private $modelplan;
    private $setting;

    public function __construct(RpPaymentPlanInstallment $model, UserSettingScreen $setting ,RpInstallmentPaymentPlan $modelplan)
    {
        $this->model = $model;
        $this->modelplan = $modelplan;
        $this->setting = $setting;
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
        return $this->modelplan->find($id);
    }


    public function create($request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->payment_plan_installments as $data):
            $model = $this->model->create($data);
            endforeach;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->modelplan->find($id);
            foreach ($model->payment_plan_installments as $data ):
                $data->delete();
            endforeach;
            foreach ($request->payment_plan_installments as $data):
                $model = $this->model->create($data);
            endforeach;
        });

    }

    public function delete($id)
    {
        $model  = $this->modelplan->find($id);
        foreach ($model->payment_plan_installments as $data ):
            $data->delete();
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
            "RpPaymentPlanInstallment",
            "RpPaymentPlanInstallment_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

}
