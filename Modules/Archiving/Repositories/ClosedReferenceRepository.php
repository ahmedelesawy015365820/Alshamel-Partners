<?php

namespace Modules\Archiving\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Archiving\Entities\ClosedReference;

class ClosedReferenceRepository implements ClosedReferenceInterface
{
    private $model;
    public function __construct(ClosedReference $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model
            ::with(
                [
                    'documentField' => function ($query) {
                        $query->select('id', 'name', 'name_e');
                    },
                ]
            )
            ->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

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
        return DB::transaction(function () use ($request) {
            cacheForget("archiveClosedReference");
            return $this->model->create($request->all());
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
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

    private function forget($id)
    {
        $keys = [
            "archiveClosedReference",
            "archiveClosedReference_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
