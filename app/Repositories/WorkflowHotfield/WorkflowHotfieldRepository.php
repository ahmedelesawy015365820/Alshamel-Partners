<?php

namespace App\Repositories\WorkflowHotfield;

use App\Models\UserSettingScreen;
use App\Models\WorkflowHotfield;
use Illuminate\Support\Facades\DB;

class WorkflowHotfieldRepository implements WorkflowHotfieldRepositoryInterface
{

    private $model;
    public function __construct(WorkflowHotfield $model)
    {
        $this->model = $model;
    }

    public function getAllWorkflowHotfields($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

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
        DB::transaction(function () use ($request) {

            $this->model->create($request);
            cacheForget("WorkflowHotfields");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request);
            $this->forget($id);

        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function setting($request)
    {
        DB::transaction(function () use ($request) {
            $screenSetting = UserSettingScreen::where('user_id', $request['user_id'])->where('screen_id', $request['screen_id'])->first();
            $request['data_json'] = json_encode($request['data_json']);
            if (!$screenSetting) {
                UserSettingScreen::create($request);
            } else {
                $screenSetting->update($request);
            }
        });
    }

    public function getSetting($user_id, $screen_id)
    {
        return UserSettingScreen::where('user_id', $user_id)->where('screen_id', $screen_id)->first();
    }

    private function forget($id)
    {
        $keys = [
            "WorkflowHotfields",
            "WorkflowHotfields_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }
}
