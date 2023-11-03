<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\Serial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Entities\ProductAttributeValue;
use Modules\PointOfSale\Entities\ProductVariant;
use Modules\PointOfSale\Http\Requests\ProductVariantRequest;
use Modules\PointOfSale\Transformers\ProductVariantResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariantController extends Controller
{
    public function __construct(private ProductVariant $model, private Media $media, private ProductAttributeValue $productAttributeValueModel, private Order $orderModel, private Payment $paymentModel, private Serial $serialModel)
    {
        $this->model = $model;
        $this->media = $media;
        $this->productAttributeValueModel = $productAttributeValueModel;
        $this->orderModel = $orderModel;
        $this->paymentModel = $paymentModel;
        $this->serialModel = $serialModel;

    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new ProductVariantResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ProductVariantResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ProductVariantRequest $request)
    {

        if ($request->product_type == 'variant') {

            if ($request->product_variant) {
                foreach ($request->product_variant as $variant) {
                    $model = $this->model->create([
                        'product_id' => $variant['product_id'],
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
                                'product_id' => $attribute['product_id'],
                                'attribute_id' => $attribute['attribute_id'],
                                'values' => $attribute['values'],
                                'variant_id' => $model->id
                            ]);
                        }
                    }

                    if ($request->hasFile('product_variant.0.media.0')) {
                        $uploadedMedia = $request->file('product_variant.0.media.0');
                        $fileName = $uploadedMedia->store('media', 'public');

                        $media = $this->media::create([
                            'name' => $fileName,
                            'model_id' => $model->id,
                            'model_type' => get_class($this->model),
                        ]);
                    }

                    if ($model && $variant['quantity'] > 0 && $variant['purchase_price']) {
                        $orderModel = $this->orderModel::create([
                            'date' => date('Y-m-d'),
                            'order_type' => 'receiving',
                            'type' => 'supplier',
                            'status' => 'done',
                            'sub_total' => $variant['quantity'] * $variant['purchase_price'],
                            'total' => $variant['quantity'] * $variant['purchase_price'],
                            'branch_id' => $request['branch_id'],
                            'document_id' => $request['document_id'],
                        ]);

//                        $serial = $this->serialModel::where([['branch_id', $request['branch_id']], ['document_id', $request['document_id']]])->first();
//                        $orderModel->serial_id = $serial->id ?? null;
//                        $orderModel->save();
//
//                        $serials = generalSerial($orderModel);
//                        $orderModel->update([
//                            "serial_number" => $serials['serial_number'],
//                            "prefix" => $serials['prefix'],
//                        ]);

                        if ($orderModel) {
                            OrderItem::create([
                                'product_id' => $model->product_id,
                                'variant_id' => $model->id,
                                'type' => 'receiving',
                                'quantity' => $variant['quantity'],
                                'price' => $variant['purchase_price'],
                                'sub_total' => $variant['quantity'] * $variant['purchase_price'],
                                'order_id' => $orderModel->id,
                            ]);

                            /** Create Order Payment  */

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

        } else {
            $model = $this->model->create($request->validated());

            if ($model && $request->quantity > 0 && $request->purchase_price) {

                $orderModel = $this->orderModel::create([
                    'date' => date('Y-m-d'),
                    'order_type' => 'receiving',
                    'type' => 'supplier',
                    'status' => 'done',
                    'sub_total' => $request->quantity * $request->purchase_price,
                    'total' => $request->quantity * $request->purchase_price,
                    'branch_id' => $request->branch_id,
                    'document_id' => $request->document_id,
                ]);

//                $serial = $this->serialModel::where([['branch_id', $request->branch_id], ['document_id', $request->document_id]])->first();
//                $orderModel->serial_id = $serial->id ?? null;
//                $orderModel->save();
//
//                $serials = generalSerial($orderModel);
//                $orderModel->update([
//                    "serial_number" => $serials['serial_number'],
//                    "prefix" => $serials['prefix'],
//                ]);

                if ($orderModel) {

                    OrderItem::create([
                        'product_id' => $model->product_id,
                        'variant_id' => $model->id,
                        'type' => 'receiving',
                        'quantity' => $request->quantity,
                        'price' => $request->purchase_price,
                        'sub_total' => $request->quantity * $request->purchase_price,
                        'order_id' => $orderModel->id,
                        'branch_id' => $request->branch_id ?? null,
                    ]);

                    /** Create Order Payment  */

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

        return responseJson(200, 'created');

    }

    public function update($id, ProductVariantRequest $request)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
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

        return responseJson(200, 'updated', new ProductVariantResource($model));

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

        $relationsWithChildren = $model->hasChildren();

        if (!empty($relationsWithChildren)) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
            }
            return responseJson(400, $errorMessages);
        }

        $this->model->delete($id);

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

}
