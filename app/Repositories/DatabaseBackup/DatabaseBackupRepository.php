<?php

namespace App\Repositories\DatabaseBackup;

use Illuminate\Support\Facades\DB;

class DatabaseBackupRepository implements DatabaseBackupInterface
{

    public function __construct(private \App\Models\DatabaseBackup $model)
    {
        $this->model = $model;
    }

    public function all($request)
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
        return DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());

            return $model;
        });
    }


    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    // public function storeBackupPath(string $path): void
    // {
    //     $backup = new \App\Models\DatabaseBackup();
    //     $backup->path = $path;
    //     $backup->save();
    // }
}
