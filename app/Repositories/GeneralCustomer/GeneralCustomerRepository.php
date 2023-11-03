<?php

namespace App\Repositories\GeneralCustomer;

use App\Models\GeneralCustomer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GeneralCustomerRepository implements GeneralCustomerRepositoryInterface
{

    public function __construct(private GeneralCustomer $model, private Media $media, private Supplier $supplier)
    {
        $this->model = $model;
        $this->media = $media;
        $this->supplier = $supplier;
    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where(function ($q) use ($request) {
                $q->when($request->opening_balance, function ($q) use ($request) {
                    $q->doesntHave('opening_balances');
                });
            });

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }
        if ($request->type !== null) {
            $models->where('type', $request->type);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {

            $model = $this->model->create($data);
            if (isset($data['is_supplier'])) {
                $this->createSupplier($data, $model);
            }
            return $model;
        });
    }

    public function createSupplier($data, $model)
    {

        if ($data['is_supplier'] == 1) {
            $supplier_exists = $this->supplier->where('name', $data['name'])->where('customer_id', null)->first();
            if ($supplier_exists) {
                $supplier_exists->update(['customer_id' => $model->id]);
                return $supplier_exists;
            }
            $create_supplier = collect($model)->except(['id', 'updated_at', 'created_at']);
            $supplier = $this->supplier->create($create_supplier->all());
            $supplier->update(['customer_id' => $model->id]);
            return $create_supplier;
        }
        return 'is_supplier == 0';
    }

    public function update($data, $id)
    {

        return DB::transaction(function () use ($id, $data) {
            $model = $this->model->where("id", $id)->first();
            $model->update($data->all());
            if (isset($data['is_supplier'])) {
                $this->updateSupplier($data, $model);
            }
            if (request()->media && !request()->old_media) { // if there is new media and no old media
                $model->clearMediaCollection('media');
                foreach (request()->media as $media) {
                    uploadImage($media, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if (request()->old_media && !request()->media) { // if there is old media and no new media
                $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                    $media->delete();
                });
            }

            if (request()->old_media && request()->media) { // if there is old media and new media
                $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                    $media->delete();
                });
                foreach (request()->media as $image) {
                    uploadImage($image, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if (!request()->old_media && !request()->media) { // if this is no old media and new media
                $model->clearMediaCollection('media');
            }
            return $this->model->find($id);
        });

        return $model;

    }

    public function updateSupplier($data, $model)
    {
        $supplier_customer = $this->supplier->where('name', $data['name'])->where('customer_id', $model->id)->first();

        if ($data['is_supplier'] == 1) {

            if ($supplier_customer) {

                $supplier_not_exists = $this->supplier->where('name', $data['name'])->where('customer_id', null)->first();
                if ($supplier_not_exists) {
                    $supplier_not_exists->update(['customer_id' => $model->id]);
                    return $supplier_not_exists;
                }
                $supplier_exists = $this->supplier->where('name', $data['name'])->where('customer_id', $model->id)->first();
                if (!$supplier_exists) {
                    $create_supplier = collect($model)->except(['id', 'updated_at', 'created_at']);
                    $supplier = $this->supplier->create($create_supplier->all());
                    $supplier->update(['customer_id' => $model->id]);
                    return $create_supplier;
                }

            }
        }
        if ($data['is_supplier'] == 0) {
            if ($supplier_customer) {
                if ($supplier_customer->hasChildren()) {
                    $supplier_customer->update(['customer_id' => null]);
                    $supplier_customer->delete();
                }
                return "delete";
            }
        }
        $supplier_not_exists = $this->supplier->where('name', $data['name'])->where('customer_id', null)->first();
        if ($supplier_not_exists) {
            $supplier_not_exists->update(['customer_id' => $model->id]);
            return $supplier_not_exists;
        }

        if (!$supplier_customer) {
            $this->createSupplier($data, $model);
            return "create Supplier";
        }

        return 'is_supplier == 0';

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
            "GeneralCustomer",
            "GeneralCustomer_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

    public function checkSupplier($request)
    {
        if ($request['supplier'] == 'create') {

            if ($request['is_supplier'] == 1) {

                $supplier_exists = $this->supplier->where('name', $request['name'])->where('customer_id', null)->first();
                if ($supplier_exists) {
                    return responseJson(200, 'Exists', $supplier_exists);
                }

                $supplier_customer = $this->supplier->where('name', $request['name'])->where('customer_id', '!=', null)->first();
                if ($supplier_customer) {
                    $supplier_customer->customer;
                    return responseJson(400, 'Supplier Customer', $supplier_customer);
                }

            }

        }
        if ($request['supplier'] == 'update') {

            if ($request['is_supplier'] == 1) {

                $model = $this->model->where('id', $request['id'])->where('is_supplier', 1)->first();
                if (!$model) {

                    $supplier_customer = $this->supplier->where('name', $request['name'])->where('customer_id', $request['id'])->first();
                    if (!$supplier_customer) {

                        $supplier_exists = $this->supplier->where('name', $request['name'])->where('customer_id', null)->first();
                        if ($supplier_exists) {
                            return responseJson(200, 'Exists', $supplier_exists);
                        }

                        $supplier_customer_first = $this->supplier->where('name', $request['name'])->where('customer_id', '!=', null)->first();
                        if ($supplier_customer_first) {
                            $supplier_customer_first->customer;
                            return responseJson(400, 'Supplier Customer', $supplier_customer_first);
                        }
                    }

                }

            }

            if ($request['is_supplier'] == 0) {
                $model = $this->model->where('id', $request['id'])->where('is_supplier', 1)->first();
                if ($model) {
                    return responseJson(200, 'Delete Supplier');

                }

            }

        }

        return 'is_supplier == 0';

    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e')->where(function ($q) use ($request) {
            $q->when($request->opening_balance, function ($q) use ($request) {
                $q->doesntHave('opening_balances');
            });
        });

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }
        if ($request->type !== null) {
            $models->where('type', $request->type);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
