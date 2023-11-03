<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Transformers\PurchaseReportResource;

class PurchaseReportController extends Controller
{
    public function __construct(private Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }

    public function all(Request $request)
    {

        $models = $this->orderModel->filter($request)
            ->where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        } //

        return responseJson(200, 'success', PurchaseReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function grandTotal(Request $request)
    {
        $data = [];
        $models = $this->orderModel->filter($request)
            ->where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')->get();

        $data['item_purchased_total'] = $models->sum(function ($order) {
            return $order->items->count();
        });

        $data['total_total'] = $models->sum('total');
        $data['due_amount_total'] = $models->sum('due_amount');

        return $data;
    }
}
