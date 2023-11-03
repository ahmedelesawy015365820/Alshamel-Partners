<?php

namespace App\Repositories\FinancialYear;

use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;

class FinancialYearRepository implements FinancialYearInterface
{

    public function __construct(private \App\Models\FinancialYear $model, private UserSettingScreen $setting)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            if ($request['is_active'] == 1) {
                collect($this->model->all())->each(function ($item) {
                    $item->update(["is_active" => 0]);
                });
            }
            $this->model->create($request->all());
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            if ($request['is_active'] == 1) {
                $this->model->where('id', '!=', $id)->update(['is_active' => 0]);
            }
            $this->model->where("id", $id)->update($request->all());
        });
    }

    public function delete($id)
    {

        $model = $this->find($id);
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

    public function DataOfModelFinancialYear($request)
    {
        return $this->model->whereDate('start_date', '<=', $request['date'])->whereDate('end_date', '>=', $request['date'])->where('is_active', 1)->first();
    }


}
