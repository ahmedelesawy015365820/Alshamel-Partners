<?php

namespace App\Repositories\Governorate;

use App\Models\Governorate;
use Illuminate\Support\Facades\DB;

class GovernorateRepository implements GovernorateInterface
{

    public function __construct(private Governorate $model)
    {}

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
            if ($request->is_default == 1) {
                $this->model->where('id', '!=', $model->id)->update(['is_default' => 0]);
            }
            cacheForget("governorates");
            return $model;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
            if ($request->is_default == 1) {
                $this->model->where('id', '!=', $id)->update(['is_default' => 0]);
            }
            // $this->forget($id);

        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        // $this->forget($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    private function forget($id)
    {
        $keys = [
            "governorates",
            "governorates_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

    public function getName($request)
    {

        $models = $this->model->select('id', 'name', 'name_e','country_id')->where('is_active', 'active')
        ->when($request->country_id, function ($q) use ($request) {
            $q->where('country_id', $request->country_id);
        });

        if($request->country_ids){
            $models->whereIn('country_id',$request->country_ids);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }
}
