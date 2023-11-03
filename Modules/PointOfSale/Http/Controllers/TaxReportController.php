<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Transformers\TaxReportResource;

class TaxReportController extends Controller
{
    public function __construct(private Order $order)
    {
        $this->order = $order;
    }

    public function all(Request $request)
    {
        $models = $this->order->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');



        if ($request->branch_id) {
            $models->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->order_type) {
            $models->where('order_type', $request->order_type);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        } //

        return responseJson(200, 'success', TaxReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function grandTotal(Request $request)
    {
        $data = [];
        $query = $this->order->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        };

        if ($request->branch_id) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->order_type) {
            $query->where('order_type', $request->order_type);
        }

        $models = $query->get();

        $data['total'] = $models->sum('total');
        $data['tax'] = $models->sum('total_tax');

        return $data;
    }
}
