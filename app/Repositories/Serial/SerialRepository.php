<?php

namespace App\Repositories\Serial;

use App\Models\Serial;
use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;

class SerialRepository implements SerialRepositoryInterface
{
    public $model;
    public function __construct(Serial $model, private UserSettingScreen $setting)
    {
        $this->model = $model;
        $this->setting = $setting;
    }
    public function getAll($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }

        if ($request->document_id) {
            $models->where('document_id', $request->document_id);
        }

        if ($request->company_id) {
            $models->where('company_id', $request->company_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create(array $data)
    {

        $serial = $this->model
            ->where('document_id', $data['document_id'])
            ->where('branch_id', $data['branch_id'])
            ->first();

        if ($serial) {
            return null;
        }

        return DB::transaction(function () use ($data) {
            return $model = $this->model->create($data);
            // if (request()->is_default == 1) {
            //     $this->model->where('id', '!=', $model->id)->update(['is_default' => 0]);
            // }

        });

    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->validated());
            // if (request()->is_default == 1) {
            //     $this->model->where('id', '!=', $id)->update(['is_default' => 0]);
            // }
            // $this->forget($id);
        });

        $model = $this->model->find($id);
        return $model;
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

    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }

        if ($request->document_id) {
            $models->where('document_id', $request->document_id);
        }

        if ($request->company_id) {
            $models->where('company_id', $request->company_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
