<?php

namespace Modules\ClubMembers\Repositories\CmSponser;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmSponser;
use Modules\RealEstate\Entities\RlstCategoriesItem;

class CmSponserRepository implements CmSponserInterface
{

    public function __construct(private CmSponser $model)
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

    public function getRootNodes()
    {
        return $this->model->where("parent_id", 0)->get();
    }

    public function getChildNodes($parentId)
    {
        return $this->model->where("parent_id", $parentId)->get();
    }

    public function create($request)
    {
        return DB::transaction(function () use ($request) {
            return $this->model->create($request->all());
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
        });

        $model = $this->model->find($id);

        return $model;
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

}
