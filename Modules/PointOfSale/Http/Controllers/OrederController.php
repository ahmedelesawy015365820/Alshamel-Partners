<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\Serial;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Entities\Product;
use Modules\PointOfSale\Entities\ProductVariant;
use Modules\PointOfSale\Http\Requests\CreateOrederRequest;
use Modules\PointOfSale\Http\Requests\CreatePurchasesProductsRequest;
use Modules\PointOfSale\Http\Requests\CreateReturnPurchasesProductsRequest;
use Modules\PointOfSale\Transformers\GetReturnOrderResource;
use Modules\PointOfSale\Transformers\OrderHolderResource;
use Modules\PointOfSale\Transformers\OrderResource;

class OrederController extends Controller
{
    public function __construct(private Product $productModel, private Order $orderModel, private ProductVariant $productVariantModel, private OrderItem $orderItemModel, private Payment $paymentModel, private Serial $serialModel)
    {

        $this->productModel = $productModel;
        $this->orderModel = $orderModel;
        $this->productVariantModel = $productVariantModel;
        $this->orderItemModel = $orderItemModel;
        $this->paymentModel = $paymentModel;
        $this->serialModel = $serialModel;

    }

    public function all(Request $request)
    {
        $models = $this->orderModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->status) {
            $models->where('status', $request->status);
        }

        if ($request->order_type) {
            $models->where('order_type', $request->order_type);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limit) {
            $models = ['data' => $models->take($request->limit)->get(), 'paginate' => false];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        if ($request->status) {
            return responseJson(200, 'success', OrderHolderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
        }

        return responseJson(200, 'success', OrderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function createSerial($order, $request)
    {
        $serial = $this->serialModel::where([['branch_id', $request['branch_id']], ['document_id', $request['document_id']]])->first();
        if ($serial) {
            $order->serial_id = $serial->id ?? null;
            $order->save();
            $serials = generalSerial($order);
            $order->update([
                "serial_number" => $serials['serial_number'],
                "prefix" => $serials['prefix'],
            ]);
            return " Success Create Serial";
        }
        return " Branch_id  Or Document_id Not Found";
    }

    public function updateOrderReturn($order_id, $return_type)
    {
        $order = $this->orderModel->find($order_id);
        if ($order) {
            $order->update([
                'return_type' => $return_type,
            ]);
            return "Success Update filed return_type ";
        }
        return "Order Not Found ";
    }

    public function allCreate($request)
    {

        if (isset($request['order_id'])) {
            $attributes = $this->calculateReturnOrder($request);

        } else {
            $attributes = $this->calculateOrder($request);
        }
        if (isset($request['order_id'])) {
            $order = $this->orderModel->find($request['order_id']);
            $order->update([
                'return_type' => $request['order_return_type'],
            ]);
        }
        /** Create Order  */
        $create_order = array_merge($request, [
            'date' => now()->format('Y-m-d'),
            'sub_total' => $attributes['sub_total'],
            'total' => $attributes['total'],
            'profit' => $attributes['profit'],
            'total_tax' => $attributes['total_tax'],
            'status' => "done",

        ]);
        $order = $this->orderModel->create(collect($create_order)->except(['products'])->all());

        /** Create Serial  */
        $this->createSerial($order, $request);

        /** Create Order Items  */
        if ($request['type'] == 'supplier') {
            $number = 1;
        }
        if ($request['type'] == 'customer') {
            $number = -1;

        }
        foreach ($request['products'] as $product):
            if ($request['type'] == "supplier") {
                $type = isset($product['product_variant_id']) ? "receiving" : "discount";
            } else {
                $type = isset($product['product_variant_id']) ? "sales" : "discount";
            }
            $order_items = $this->orderItemModel->create([
                'product_id' => $product['product_id'] ?? 0,
                'variant_id' => $product['product_variant_id'] ?? 0,
                'type' => $type,
                'quantity' => isset($product['product_variant_id']) ? $product['qty'] * $number : 1,
                'price' => $product['price'],
                'discount' => $product['discount'] ?? 0,
                'sub_total' => isset($product['product_variant_id']) ? ($product['price'] * $product['qty']) - ($product['price'] * $product['qty']) * $product['discount'] / 100 : $product['price'] * $number,
                'order_id' => $order->id,
                'note' => $product['note'],
                'tax_id' => $product['tax_id'] ?? null,
                'parent_id' => $product['parent_id'] ?? null,
            ]);

        endforeach;

        foreach ($request['payments'] as $payment):

            $this->paymentModel->create([
                'paid' => $payment['paid'],
                'payment_method_id' => $payment['payment_method_id'] ?? 1,
                'date' => now()->format('Y-m-d'),
                'order_id' => $order->id,
                'is_active' => $payment['is_active'] ?? 1,
                'exchange' => $payment['exchange'] ?? 0,
            ]);

        endforeach;

        return $order;
    }

    public function calculateOrder($request)
    {
        $attributes = [];
        $sub_total = 0;
        $purchase_price = 0;
        $sub_price = 0;
        $sub_tax = 0;
        $total_tax = 0;
        $sub_discount = $request['sub_discount'] ?? 0;

        foreach ($request['products'] as $product):

            if (isset($product['product_variant_id'])) {

                $item = $this->productVariantModel->find($product['product_variant_id']);
                $tax = $this->productModel->with('tax')->find($product['product_id']);

                $tax_percentage = $tax['tax']['percentage'] ?? 0;
                $discount = $product['discount'] ?? 0;

                $sub_price = $product['qty'] * $product['price'];
                $total_price = $sub_price - ($sub_price * ($discount / 100));
                $sub_tax = $total_price * ($tax_percentage / 100);
                $total_tax += $sub_tax;
                $sub_total += $total_price;
                $purchase_price += $product['qty'] * $item->purchase_price;
            }
        endforeach;
        $attributes['total_tax'] = $total_tax;
        $attributes['sub_total'] = $sub_total;
        $attributes['profit'] = ($attributes['sub_total'] - $purchase_price) - $sub_discount;

        if (!isset($request['tax_type'])) {
            $attributes['total'] = ($attributes['sub_total'] - $sub_discount) + $attributes['total_tax'];
        } else {
            if ($request['tax_type'] == 'excluded') {
                $attributes['total'] = $attributes['sub_total'] - $sub_discount;
            }
            if ($request['tax_type'] == 'included') {
                $attributes['total'] = ($attributes['sub_total'] - $sub_discount) + $attributes['total_tax'];
            }
        }
        if ($request['type'] == 'supplier') {
            $attributes['profit'] = 0;
        }

        if ($request['type'] == 'supplier') {
            $attributes['profit'] = 0;
        }

        return $attributes;
    }

    public function createOrders(CreateOrederRequest $request)
    {
        return $this->allCreate($request->validated());
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function returnOrder(CreateOrederRequest $request)
    {
        return $this->allCreate($request->validated());
    }

    public function calculateReturnOrder($request)
    {
        $order = $this->orderModel->find($request['order_id']);
        $percent = ($order['sub_discount'] / $order['sub_total']) * 100;
        $item_percent = round($percent, 1);

        $attributes = [];
        $sub_total = 0;
        $purchase_price = 0;
        $sub_tax = 0;
        $total_tax = 0;
        $total = 0;
        $sub_discount = $request['sub_discount'] ?? 0;
        $item_sub_discount_price = 0;
        foreach ($request['products'] as $product):

            $item = $this->productVariantModel->find($product['product_variant_id']);

            $tax = $this->productModel->with('tax')->find($product['product_id']);

            $all_discount = $product['discount'] ?? 0;

            $price_product = $product['price'] - ($product['price'] * ($all_discount / 100));

            $count_price_product = $price_product * $product['qty'];

            $tax_percentage = $tax['tax']['percentage'] ?? 0;

            $item_sub_discount_price += round(($price_product * ($item_percent / 100)), 2) * $product['qty'];

            $sub_total += $count_price_product;

            $sub_tax = ($price_product * ($tax_percentage / 100)) * $product['qty'];

            $total_tax += $sub_tax;

            $purchase_price += $product['qty'] * $item->purchase_price;

        endforeach;
        $attributes['total_tax'] = $total_tax;
        $attributes['sub_total'] = $sub_total;
        if ($order->tax_type == 'excluded') {
            $attributes['total'] = $attributes['sub_total'] - $sub_discount;
            $total = $attributes['sub_total'] - $item_sub_discount_price;
        }
        if ($order->tax_type == 'included') {
            $total = ($attributes['sub_total'] + $attributes['total_tax']) - $item_sub_discount_price;
        }
        $attributes['total'] = $total;
        $attributes['profit'] = ($attributes['sub_total'] - $purchase_price) - $sub_discount;

        return $attributes;
    }

    public function getReturnOrder(Request $request)
    {
        $models = $this->orderModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->whereNull('returned_invoice')->where('return_type', null)->orWhere('return_type', 'partial');

        if ($request->status) {
            $models->where('status', 'done');
        }
        if ($request->order_type) {
            $models->where('order_type', $request->order_type);
        }

        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limit) {
            $models = ['data' => $models->take($request->limit)->get(), 'paginate' => false];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        if ($request->status) {
            return responseJson(200, 'success', GetReturnOrderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
        }

        return responseJson(200, 'success', GetReturnOrderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function createPurchasesProducts(CreatePurchasesProductsRequest $request)
    {
        return $this->allCreate($request->validated());

    }

    public function holdOrders(CreateOrederRequest $request)
    {
        // $request = $request->validated();


        $attributes = $this->calculateOrder($request);
        /** Create Order  */

        $create_order = array_merge($request->all(), [
            'date' => now()->format('Y-m-d'),
            'sub_total' => $attributes['sub_total'],
            'total' => $attributes['total'],
            'profit' => $attributes['profit'],
            'total_tax' => $attributes['total_tax'],
            'status' => "hold",

        ]);

        $order = $this->orderModel->create(collect($create_order)->except(['products'])->all());

        $serial = $this->serialModel::where([['branch_id', $request->branch_id], ['document_id', $request->document_id]])->first();
        $order->serial_id = $serial->id ?? null;
        $order->save();

        $serials = generalSerial($order);
        $order->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);

        /** Create Order Items  */

        foreach ($request['products'] as $product):
            $order_items = $this->orderItemModel->create([
                'product_id' => $product['product_id'] ?? 0,
                'variant_id' => $product['product_variant_id'] ?? 0,
                'tax_id' => $product['tax_id'] ?? null,
                'type' => isset($product['product_variant_id']) ? "sales" : "discount",
                'quantity' => isset($product['product_variant_id']) ? $product['qty'] * -1 : 1,
                'price' => $product['price'],
                'discount' => $product['discount'] ?? 0,
                'sub_total' => isset($product['product_variant_id']) ? ($product['price'] * $product['qty']) - ($product['price'] * $product['qty']) * $product['discount'] / 100 : $product['price'] * -1,
                'order_id' => $order->id,
                'note' => $product['note'],
            ]);

        endforeach;

        return $order;

    }

    public function updateHoldOrders(CreateOrederRequest $request, $order_id)
    {
        $request = $request->validated();

        $order = $this->orderModel->findOrFail($order_id);

        $updatedAttributes = $this->calculateOrder($request);

        $order->update([
            'sub_total' => $updatedAttributes['sub_total'],
            'total' => $updatedAttributes['total'],
            'profit' => $updatedAttributes['profit'],
            'total_tax' => $updatedAttributes['total_tax'],
            'date' => now()->format('Y-m-d'),
            'status' => "done",
        ]);

        $order->items()->delete();

        foreach ($request['products'] as $product) {
            $orderItem = $this->orderItemModel->create([
                'product_id' => $product['product_id'] ?? 0,
                'variant_id' => $product['product_variant_id'] ?? 0,
                'tax_id' => $product['tax_id'] ?? null,
                'type' => isset($product['product_variant_id']) ? "sales" : "discount",
                'quantity' => isset($product['product_variant_id']) ? $product['qty'] * -1 : 1,
                'price' => $product['price'],
                'discount' => $product['discount'] ?? 0,
                'sub_total' => isset($product['product_variant_id']) ? ($product['price'] * $product['qty']) - ($product['price'] * $product['qty']) * $product['discount'] / 100 : $product['price'] * -1,
                'order_id' => $order->id,
                'note' => $product['note'],
            ]);
        }

        /** Create Order Payment  */
        foreach ($request['payments'] as $payment):

            $this->paymentModel->create([
                'paid' => $payment['paid'],
                'payment_method_id' => $payment['payment_method_id'] ?? 1,
                'date' => now()->format('Y-m-d'),
                'order_id' => $order->id,
                'is_active' => $payment['is_active'] ?? 1,
                'exchange' => $payment['exchange'],
            ]);

        endforeach;

        return $order;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function createReturnPurchasesProducts(CreateReturnPurchasesProductsRequest $request)
    {

        $total = 0;
        $all_request = $request->validated();

        /** Update Parent Order Filed return_type   */
        $this->updateOrderReturn($all_request['order_id'], $all_request['order_return_type']);

        /** NEW Create Order  */
        $order = $this->orderModel->create($all_request);

        /** Create Serial Order  */
        $this->createSerial($order, $all_request);

        /** Create  Order  Items */
        foreach ($all_request['products'] as $product):
            $Item = $this->orderItemModel->create(
                array_merge($product, [
                    'type' => 'receiving',
                    'order_id' => $order->id,
                    'variant_id' => $product['product_variant_id'],
                    'quantity' => $product['qty'],
                    'sub_total' => $product['price'] * $product['qty'],
                ])
            );
            $total += $Item['sub_total'];
        endforeach;

        /** Update Order Filed total  */
        $order->update(['total' => $total]);

        /** Create  Payment */
        foreach ($all_request['payments'] as $payment):
            $this->paymentModel->create(array_merge($payment, [
                'date' => $all_request['date'],
                'order_id' => $order->id,
            ]));
        endforeach;

        return $order;

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('pointofsale::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('pointofsale::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
        $model = $this->orderModel->where('status', 'hold')->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->items()->delete();
        $model->delete();

        return responseJson(200, 'deleted');
    }

}
