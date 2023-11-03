<?php


namespace Modules\RecievablePayable\Repositories;


use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;
use Modules\RecievablePayable\Entities\RpInstallmentCondation;

class RpInstallmentCondationRepository implements RpInstallmentCondationRepositoryInterface
{
    private $model;
    private $setting;

    public function __construct(RpInstallmentCondation $model, UserSettingScreen $setting)
    {
        $this->model = $model;
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

    public function create($request)
    {
        checkIsDefaultGeneral($request['is_default'],$this->model);
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            cacheForget("RpInstallmentStatus");
        });
    }

    public function update($request, $id)
    {
        checkIsDefaultGeneral($request['is_default'],$this->model);
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            $model->update($request->all());
            $this->forget($id);
        });
    }

    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
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
            "RpInstallmentStatus",
            "RpInstallmentStatus_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }
}
