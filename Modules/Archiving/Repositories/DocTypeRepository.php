<?php

namespace Modules\Archiving\Repositories;

use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\NotFoundException;
use Modules\Archiving\Entities\ArchDocumentStatus;
use Modules\Archiving\Entities\DocType;
use Modules\Archiving\Entities\DocTypeField;

class DocTypeRepository implements DocTypeInterface
{
    private $model;
    public function __construct(DocType $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->where(function ($q) use ($request) {
            // return parent which not have children and children for parents
            if ($request->select) {
                $q->whereNull('parent_id')->orWhereHas('children');
            }
        })
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
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
            cacheForget("ArchDoctype");
            $data = $this->model->create($request->all());
            if ($request->parent_id) {
                $model = $this->model->find($request->parent_id);
                // doc type field
                $doc_type_fields = DocTypeField::where("doc_type_id", $model->id)->get();
                // dd($doc_type_fields);
                foreach ($doc_type_fields as $doc_type_field) {
                    DocTypeField::create(
                        [
                            "doc_type_id" => $data->id,
                            "doc_field_id" => $doc_type_field->doc_field_id,
                            "is_required" => $doc_type_field->is_required,
                            "field_order" => $doc_type_field->field_order,
                            "parent_id" => $model->id,
                        ]
                    );
                }

                $ids = \Modules\Archiving\Entities\DocTypeDepartment::where("arch_doc_type_id", $model->id)->pluck("arch_department_id")->toArray();

                foreach ($ids as $id) {
                    \Modules\Archiving\Entities\DocTypeDepartment::create([
                        "arch_doc_type_id" => $data->id,
                        "arch_department_id" => $id,
                        'parent_id' => $model->id,
                    ]);
                }

                $ids = $model->statuses()->pluck("status_id")->toArray();
                $model->statuses()->attach($ids);
            }
            return $data;
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

    public function tree()
    {
        return $this->model->whereNull('parent_id')->with('children')->get();
    }

    public function nodesLevelTwo($request)
    {
        // level two
        $models = $this->model->whereNull('parent_id');
        //        $models = $this->model->whereIn('parent_id', $parents);
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    private function forget($id)
    {
        $keys = [
            "ArchDoctype",
            "ArchDoctype_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
    //
    public function addStatusToDocumentType($request)
    {
        $docType = $this->model->find($request->doc_type_id);
        if (!$docType) {
            throw new NotFoundException();
        }
        $docType->statuses()->attach($request->status_id);
    }

    public function removeStatusFromDocumentType($doc_type_id, $status_id)
    {
        $screen = $this->model->find($doc_type_id);
        if (!$screen) {
            throw new NotFoundException();
        }
        $screen->statuses()->detach($status_id);
    }

    public function getDocTypeStatuses($docTypeId)
    {
        $docType = $this->model->find($docTypeId);
        return $docType->statuses;
    }

    public function documentStatusExist($status_id, $doc_type_id)
    {
        return ArchDocumentStatus::where("status_id", $status_id)->where("document_id", $doc_type_id)
            ->count() > 0;
    }
}
