<?php

namespace App\Repositories\Avenue;

use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;

class AvenueRepository implements AvenueInterface
{

    public function __construct(private \App\Models\Avenue $model, private UserSettingScreen $setting)
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
        return DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            return $model;
            // cacheForget("avenues");
        });
    }

    public function update($request, $id)
    {

        return DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
            return $this->model->find($id);
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function getSetting($user_id, $screen_id)
    {
        return $this->setting->where('user_id', $user_id)->where('screen_id', $screen_id)->first();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e')->where('is_active', 'active');

        if ($request->country_id) {
            $models->where('country_id', $request->country_id);
        }

        if ($request->governorate_id) {
            $models->where('governorate_id', $request->governorate_id);
        }
        if ($request->city_id) {
            $models->where('city_id', $request->city_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
