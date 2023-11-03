<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Entities\Product;
use Modules\PointOfSale\Entities\ProductAttributeValue;
use Modules\PointOfSale\Entities\ProductVariant;
use Modules\PointOfSale\Http\Requests\ProductRequest;
use Modules\PointOfSale\Http\Requests\UpdateProductRequest;
use Modules\PointOfSale\Transformers\ProductResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductController extends Controller
{
    public function __construct(private Product $model, private Media $media, private User $userModel,
        private ProductAttributeValue $productAttributeValueModel, private ProductVariant $productVariantModel, private Order $orderModel, private Payment $paymentModel) {

        $this->model = $model;
        $this->userModel = $userModel;
        $this->media = $media;
        $this->productAttributeValueModel = $productAttributeValueModel;
        $this->productVariantModel = $productVariantModel;
        $this->orderModel = $orderModel;
        $this->paymentModel = $paymentModel;

    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new ProductResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ProductResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            // $data['product_type'] = $request->type == 0 ? 'standard' : 'variant';
            // $data['created_by'] = Auth::user()->id;

            if ($data['tax_id'] == 'no-tax') {
                $data['taxable'] = 0;
            } elseif ($data['tax_id'] == 'default-tax') {
                $data['taxable'] = 1;
                $data['tax_type'] = 'default';
            } else {
                $data['taxable'] = 1;
                $data['tax_type'] = 'custom';
                $data['tax_id'] = $data['tax_id'];
            }

            $model = $this->model->create($data);
            $model->refresh();

            if ($request->media) {
                foreach ($request->media as $media) {
                    $this->media::where('id', $media)->update([
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }
            return responseJson(200, 'created', new ProductResource($model));
        });
    }

    public function update($id, UpdateProductRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return DB::transaction(function () use ($request, $id) {

            $data = $request->validated();

            if (!$request->old_media && !$request->media) {
                if (isset($data['tax_id'])) {
                    if ($data['tax_id'] == 'no-tax') {
                        $data['taxable'] = 0;
                    } elseif ($data['tax_id'] == 'default-tax') {
                        $data['taxable'] = 1;
                        $data['tax_type'] = 'default';
                    } else {
                        $data['taxable'] = 1;
                        $data['tax_type'] = 'custom';
                        $data['tax_id'] = $data['tax_id'];
                    }
                }
            }

            $model = $this->model->findOrFail($id);
            $model->update($data);
            $model->refresh();
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

            if ($request->product_type == 'variant') {

                if ($request->product_variant) {

                    $allVariant = $this->productVariantModel->where('product_id', $model->id)->get();

                    $allAttribute = $this->productAttributeValueModel->where('product_id', $model->id)->get();

                    foreach ($allVariant as $var) {
                        $orderItems = $var->orderItem;
                        if ($orderItems->isNotEmpty()) {
                            foreach ($orderItems as $orderItem) {
                                $order = $orderItem->order;

                                if ($order) {
                                    $payments = $order->payments;

                                    if ($payments->isNotEmpty()) {
                                        foreach ($payments as $payment) {
                                            $payment->delete();
                                        }
                                    }
                                    $order->delete();
                                }
                                $orderItem->delete();
                            }
                        }
                        $var->delete();
                    }

                    foreach ($allAttribute as $attribute) {
                        $attribute->delete();
                    }

                    foreach ($request->product_variant as $variant) {

                        $variantModel = $this->productVariantModel->create([
                            'product_id' => $model->id,
                            'sku' => $variant['sku'],
                            'variant_title' => $variant['variant_title'],
                            'attribute_values' => $variant['attribute_values'],
                            'variant_details' => $variant['variant_title'],
                            'purchase_price' => $variant['purchase_price'],
                            'selling_price' => $variant['selling_price'],
                            'enabled' => $variant['enabled'] ?? true,
                            'isNotify' => $variant['isNotify'],
                            'bar_code' => $variant['bar_code'],
                            're_order' => $variant['re_order'],
                        ]);

                        if ($variant['product_attributes']) {

                            foreach ($variant['product_attributes'] as $attribute) {
                                $this->productAttributeValueModel->create([
                                    'product_id' => $model->id,
                                    'attribute_id' => $attribute['attribute_id'],
                                    'values' => $attribute['values'],
                                    'variant_id' => $variantModel->id
                                ]);
                            }
                        }

                        if (isset($variant['product_variant'][0]['media'][0])) {
                            // Delete media records with the corresponding model_id and model_type
                            $variantModel->media()
                                ->where('model_id', $variantModel->id)
                                ->where('model_type', get_class($this->productVariantModel))
                                ->delete();

                            foreach ($variant['product_variant.0.media.0'] as $uploadedMedia) {
                                // Create new media record
                                $media = $this->media::create([
                                    'name' => $uploadedMedia->store('media', 'public'),
                                    'model_id' => $variantModel->id,
                                    'model_type' => get_class($this->productVariantModel),
                                ]);
                            }
                        }

                        if ($variantModel && $variant['quantity'] > 0 && $variant['purchase_price']) {
                            $orderModel = $this->orderModel::create([
                                'date' => date('Y-m-d'),
                                'order_type' => 'receiving',
                                'type' => 'supplier',
                                'status' => 'done',
                                'sub_total' => $variant['quantity'] * $variant['purchase_price'],
                                'total' => $variant['quantity'] * $variant['purchase_price'],
                                'branch_id' => $request['branch_id'] ?? null,
                            ]);

                            $orderModel->invoice_id = $orderModel->id;
                            $orderModel->save();

                            if ($orderModel) {
                                OrderItem::create([
                                    'product_id' => $model->id,
                                    'variant_id' => $variantModel->id,
                                    'type' => 'receiving',
                                    'quantity' => $variant['quantity'],
                                    'price' => $variant['purchase_price'],
                                    'sub_total' => $variant['quantity'] * $variant['purchase_price'],
                                    'order_id' => $orderModel->id,
                                ]);

                                $this->paymentModel->create([
                                    'paid' => $orderModel->total,
                                    'payment_method_id' => 1,
                                    'date' => now()->format('Y-m-d'),
                                    'order_id' => $orderModel->id,
                                    'is_active' => 1,
                                    'options' => null,
                                    'cash_register_id' => null,
                                ]);

                            }
                        }

                    }
                }
            }

            if ($request->product_type == 'standard') {
                $productVariantModel = $this->productVariantModel->where('product_id', $model->id)->first();

                if ($productVariantModel) {
                    // $orderItem = $productVariantModel->orderItem;
                    $orderItems = $productVariantModel->orderItem()->get();

                    foreach ($orderItems as $orderItem) {
                        $order = $orderItem->order;

                        if ($order) {
                            $payments = $order->payments;
                            if ($payments) {
                                foreach ($payments as $payment) {
                                    $payment->delete();
                                }
                            }
                            $order->delete();
                        }
                        $orderItem->delete();
                    }
                    $productVariantModel->delete();
                }

                $productVariantModel2 = $this->productVariantModel->create([
                    'product_id' => $model->id,
                    'sku' => $request['sku'],
                    'variant_title' => $request['variant_title'],
                    'attribute_values' => $request['attribute_values'],
                    'variant_details' => $request['variant_details'],
                    'purchase_price' => $request['purchase_price'],
                    'selling_price' => $request['selling_price'],
                    'enabled' => $request['enabled'] ?? true,
                    'isNotify' => $request['isNotify'],
                    'bar_code' => $request['bar_code'],
                    're_order' => $request['re_order'],
                ]);

                if ($productVariantModel2 && $request->quantity > 0 && $request->purchase_price) {
                    $orderModel = $this->orderModel::create([
                        'date' => date('Y-m-d'),
                        'order_type' => 'receiving',
                        'type' => 'supplier',
                        'status' => 'done',
                        'sub_total' => $request->quantity * $request->purchase_price,
                        'total' => $request->quantity * $request->purchase_price,
                        'branch_id' => $request->branch_id ?? null,
                    ]);
                    $orderModel->invoice_id = $orderModel->id;
                    $orderModel->save();

                    if ($orderModel) {
                        OrderItem::create([
                            'product_id' => $model->id,
                            'variant_id' => $productVariantModel2->id,
                            'type' => 'receiving',
                            'quantity' => $request->quantity,
                            'price' => $request->purchase_price,
                            'sub_total' => $request->quantity * $request->purchase_price,
                            'order_id' => $orderModel->id,

                        ]);

                        $this->paymentModel->create([
                            'paid' => $orderModel->total,
                            'payment_method_id' => 1,
                            'date' => now()->format('Y-m-d'),
                            'order_id' => $orderModel->id,
                            'is_active' => 1,
                            'options' => null,
                            'cash_register_id' => null,
                        ]);

                    }
                }
            }

            return responseJson(200, 'updated', new ProductResource($model));
        });

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
            return responseJson(404, __('message.data not found'));
        }

        $salesOrderItemsCount = $model->orderItems()->where('type', 'sales')->count();

        if ($salesOrderItemsCount > 0) {
            return responseJson(400, "This item has {$salesOrderItemsCount} sales-related OrderItems and can't be deleted. Remove its OrderItems first.");
        }

        $model->product_variant()->delete();
        $model->product_attribute_value()->delete();
        $model->attributes()->delete();

        $orderItemIds = $model->orderItems()->pluck('id')->toArray();
        Order::whereIn('id', $orderItemIds)->delete();

        $model->orderItems()->delete();

        $model->delete();

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->model->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $this->model->delete($id);
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
                }

                $errorMessages[] = implode(' ', $relationErrorMessages);
            }

            return responseJson(400, $errorMessages);
        }

        return responseJson(200, __('Done'));
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }

    public function getUserLogin()
    {

        $employee = $this->userModel->where('id', auth()->user()->id)->first();

        if (!$employee) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $responseData = [
            'message' => 'success',
            'data' => new UserResource($employee),
            'pagination' => null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

    public function inventories(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->category_id) {
            $models->where('category_id', $request->category_id);
        }
        if ($request->brand_id) {
            $models->where('brand_id', $request->brand_id);
        }
        if ($request->unit_id) {
            $models->where('unit_id', $request->unit_id);
        }
        if ($request->group_id) {
            $models->where('group_id', $request->group_id);
        }
        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }
        if ($request->start_date && $request->end_date) {
            $models->whereHas('orderItems', function ($q) use ($request) {
                $q->whereHas('order', function ($qu) use ($request) {
                    $qu->whereDate('date', '>=', $request->start_date)->whereDate('date', '<=', $request->end_date);
                });
            });
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ProductResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }
}
