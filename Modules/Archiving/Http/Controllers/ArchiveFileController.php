<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Requests\UpdateArchiveFileRequest;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Archiving\Entities\ArchiveFile;
use Modules\Archiving\Entities\DocType;
use Modules\Archiving\Http\Requests\ArchiveFileRequest;
use Modules\Archiving\Http\Requests\ToggleFavouriteRequest;
use Modules\Archiving\Transformers\ArchiveFileRelationResource;
use Modules\Archiving\Transformers\ArchiveFileResource;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArchiveFileController extends Controller
{

    public function __construct(private ArchiveFile $model, private Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new ArchiveFileResource($model));
    }//ff
    public function all(AllRequest $request)
    {
        $models = $this->model->with("media")->where(function ($q) use ($request) {
            $arch_doc_type_id = is_array(request()->arch_doc_type_id) ? request()->arch_doc_type_id : [request()->arch_doc_type_id];
            if (request()->arch_doc_type_id) {
                $q->whereIn("arch_doc_type_id", $arch_doc_type_id);
            }

            if ($request->favourite == "true") {
                $q->whereHas("favourites", function ($q) use ($request) {
                    $favourite = null;
                    $user_id = $request->header('user_id');
                    $admin_id = $request->header('admin_id');

                    if ($user_id != null && $user_id != "null") {
                        $q->where("user_id", $user_id);
                    } elseif ($admin_id) {
                        $q->where("admin_id", $admin_id);
                    }
                });
            }
        })->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        $field = $request->field;
        $field = json_decode($field, true);

        if ($field) {

            if ($field['range'] == true) {
                $res = $models->get();
                $res = $res->map(function ($item) use ($field) {
                    $q = collect($item->data_type_value);
                    $q = $q->whereBetween('value', [$field['from'], $field['to']])->where('data_type', $field['data_type']);
                    if (count($q) > 0) {
                        return $item->id;
                    }
                })
                    ->reject(function ($i) {
                        return $i == null;
                    });
            } else {
                $res = $models->get();
                $res = $res->map(function ($item) use ($field) {
                    $q = collect($item->data_type_value);
                    $q = $q->where('value', $field['text'])->where('data_type', $field['data_type']);
                    if (count($q) > 0) {
                        return $item->id;
                    }
                })->reject(function ($i) {
                    return $i == null;
                });
            }
            $ids = $res->values();
            $models = $models->whereIn('id', $ids);
        }
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', ArchiveFileResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ArchiveFileRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();
        return responseJson(200, 'created', new ArchiveFileResource($model));
    }

    public function update($id, UpdateArchiveFileRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($request->media && !$request->old_media) { // if there is new media and no old media
            $model->clearMediaCollection('media');
            foreach ($request->media as $media) {
                uploadImage($media, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if ($request->old_media && !$request->media) { // if there is old media and no new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
        }

        if ($request->old_media && $request->media) { // if there is old media and new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
            foreach ($request->media as $image) {
                uploadImage($image, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }
        if (!$request->old_media && !$request->media) { // if this is no old media and new media
            $model->clearMediaCollection('media');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new ArchiveFileResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function pdf($id)
    {
        return DB::transaction(function () use ($id) {
            // set_time_limit(0);
            $model = $this->model->find($id);
            if (!$model) {
                return responseJson(404, 'not found');
            }
            $path = public_path(is_object($model->data_type_value[0]->value) ? $model->data_type_value[0]->value->name_e : $model->data_type_value[0]->value . "-" . rand(1111, 999) . '.pdf');
            $data = [
                'id' => $model->id,
                "data_type_value" => $model->data_type_value,
                "media" => isset($model->files) ? \App\Http\Resources\FileResource::collection($model->files) : null,
                "media_count" => count((array) $model->files),
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ];
            $oMerger = \Webklex\PDFMerger\Facades\PDFMergerFacade::init();
            $pdfs = $model->files->where('mime_type', 'application/pdf');
            $lastPdf = $pdfs->last();
            if ($lastPdf) {
                Pdf::loadView('pdf', $data)->save($path);
                // $oMerger->addPDF($path, 'all');
                $oMerger->addPDF($lastPdf->getPath(), 'all');
                $oMerger->merge('file');
                $oMerger->save($path);
                $model->addMedia($path)->toMediaCollection('media');
                $lastPdf->delete();
                $model->refresh();
                return responseJson(200, 'done', new ArchiveFileResource($model));
            } else {
                Pdf::loadView('pdf', $data)->save($path);
                $model->addMedia($path)->toMediaCollection('media');
                $model->refresh();
                return responseJson(200, 'done', new ArchiveFileResource($model));
            }
        });
    }

    public function toggleFavourite(ToggleFavouriteRequest $request)
    {
        if ($request->type == "user") {
            $model = $this->model->find($request->arch_archive_file_id);
            if (!$model) {
                return responseJson(404, 'not found');
            }
            $favourite = $model->favourites()->where('user_id', $request->user_id)->first();
            if ($favourite) {
                $favourite->delete();
                return responseJson(200, 'removed from favourites');
            }
            $model->favourites()->create([
                'user_id' => $request->user_id,
            ]);
            return responseJson(200, 'added to favourites');
        } else {
            $model = $this->model->find($request->arch_archive_file_id);
            if (!$model) {
                return responseJson(404, 'not found');
            }
            $favourite = $model->favourites()->where('admin_id', $request->admin_id)->first();
            if ($favourite) {
                $favourite->delete();
                return responseJson(200, 'removed from favourites');
            }
            $model->favourites()->create([
                'admin_id' => $request->admin_id,
            ]);
            return responseJson(200, 'added to favourites');
        }
    }

    public function sendArchvingNotification(Request $request)
    {
        $model = $this->model->find($request->id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        User::whereIn('id', $request->users)->get()->each(function ($user) use ($model, $request) {
            $user->notify(new GeneralNotification(
                [
                    'archiveFile' => $model,
                    'sender' => $request->sender,
                ],
                $request->id,
                'archiving-pdf',
                $request->title
            ));
        });
        return responseJson(200, 'success notification');
    }

    public function valueMedia(Request $request)
    {
        $model = $this->model->where('arch_department_id', $request->department_id)
            ->whereJsonContains('data_type_value', ["value" => request()->value])
            ->whereHas('docType', function ($q) use ($request) {
                $q->where('parent_id', $request->parent_arch_doc_type_id);
            })->where(function ($q) use ($request) {
                $q->when($request->arch_doc_type_id, function ($q) use ($request) {
                    $q->where('arch_doc_type_id', $request->arch_doc_type_id);
                });
            });

        if ($request->per_page) {
            $model = ['data' => $model->paginate($request->per_page), 'paginate' => true];
        } else {
            $model = ['data' => $model->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', ArchiveFileResource::collection($model['data']), $model['paginate'] ? getPaginates($model['data']) : null);
    }

    public function files_Department_Doc_Type(Request $request)
    {
        $model = $this->model->where(function ($q) use ($request) {
            $q->when($request->arch_department_id, function ($q) use ($request) {
                $q->where('arch_department_id', $request->arch_department_id);
            });
        });
        if (!$request->search) {
            $model->where(function ($q) use ($request) {
                $q->when($request->arch_doc_type_id, function ($q) use ($request) {
                    $q->whereHas('docType', function ($q) use ($request) {
                        $q->where('parent_id', $request->arch_doc_type_id);
                    });
                });
            });

        }
        if ($request->search) {
            $model->where(function ($q) use ($request) {
                $q->when($request->doc_type_id, function ($q) use ($request) {
                    $q->where('arch_doc_type_id', $request->doc_type_id);
                });
            });
        }

        if ($request->per_page) {
            $model = ['data' => $model->paginate($request->per_page), 'paginate' => true];
        } else {
            $model = ['data' => $model->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', ArchiveFileResource::collection($model['data']), $model['paginate'] ? getPaginates($model['data']) : null);
    }
    public function searchValue($value)
    {
        $model = collect($this->model->all());
        // search data_type_value
        $model = $model->filter(function ($model) use ($value) {
            $data_type_value = collect($model->data_type_value);
            $data_type_value = $data_type_value->filter(function ($data_type_value) use ($value) {
                if (is_object($data_type_value->value)) {
                    return false;
                }
                return strpos($data_type_value->value, $value) !== false;
            });
            return $data_type_value->count() > 0;
        });

        if (count($model) <= 0) {
            return responseJson(404, 'not found');
        }
        $model = $model->first();
        return responseJson(200, 'done', new ArchiveFileRelationResource($model));
    }

    public function  docTypeChildArchiveFiles(Request $request)
    {
        $models = $this->model->whereHas('docType', function ($q) use ($request) {
            $q->where('parent_id', $request->doc_type_id);
        })->get();
        $result = [];
        foreach ($models as $model) {
            $result = array_merge($result, $model->data_type_value);
        }
        $fields = [];
        foreach ($result as $item) {
            if (!$this->isExist($fields, $item)) {
                $fields[] = $item;
            }
        }
        return $fields;
    }
    //Commons
    private function isExist($result, $model)
    {
        foreach ($result as $item) {
            if ($item->name_e == $model->name_e) {
                return true;
            }
        }
        return false;
    }

    public function filesDocType($id)
    {
        $model = $this->model->whereHas('docType', function ($q) use ($id) {
            $q->where('id', $id);
        })->get();
        return responseJson(200, 'success', $model);
    }

    public function getKeys(Request $request)
    {
        $arch_file  = null;
        $subIds     = [];
        $archFiles = $this->model->get();
        foreach ($archFiles as $file) {
            $docType = DocType::find($file->arch_doc_type_id);
            if ($docType) {
                if ($docType->parent_id == $request->doc_type_id && $file->arch_department_id == $request->arch_department_id) {
                    $keyField = null;
                    foreach ($file->data_type_value as $field) {
                        if ($field->name_e == $request->key_name_e) {
                            $arch_file = $file;
                            $keyField = $field;
                            break;
                        }
                    }
                    if ($keyField && !in_array($keyField->value, $subIds)) {
                        $subIds[] = $keyField->value;
                    }
                }
            }
        }
        return  collect($subIds)->map(function ($id) use ($arch_file, $request) {
            return [
                "name" => $id, "name_e" => $id, "archive_file" => $arch_file,
                "parent_doc_id" => $request->doc_type_id, "parent_doc_type_children" => DocType::find($request->doc_type_id)->children
            ];
        });
    }
}
