<?php

namespace Modules\PointOfSale\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Transformers\SalesDetailsResource;

class SalesDetailsController extends Controller
{
    public function __construct(private OrderItem $orderItemModel, private Order $orderModel)
    {
        $this->orderItemModel = $orderItemModel;
        $this->orderModel = $orderModel;
    }

    public function all(Request $request)
    {
        $models = $this->orderItemModel->filter($request)->where('type', 'sales')
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->start_date && $request->end_date) {
            $models->whereHas('order', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        };

        // Filter for today
        if ($request->has('today')) {
            $models->whereHas('order', function ($q) use ($request) {
                $today = Carbon::today()->toDateString();
                $q->whereDate('date', $today);
            });
        }

        // Filter for last 7 days
        if ($request->has('last_7_days')) {
            $models->whereHas('order', function ($q) use ($request) {
                $sevenDaysAgo = Carbon::today()->subDays(7)->toDateString();
                $q->whereDate('date', '>=', $sevenDaysAgo);
            });
        }

        // Filter for last 30 days
        if ($request->has('last_30_days')) {
            $models->whereHas('order', function ($q) use ($request) {
                $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
                $q->whereDate('date', '>=', $thirtyDaysAgo);
            });
        }

        // Filter for last month
        if ($request->has('last_month')) {
            $models->whereHas('order', function ($q) use ($request) {
                $firstDayOfLastMonth = Carbon::today()->subMonth()->firstOfMonth()->toDateString();
                $lastDayOfLastMonth = Carbon::today()->subMonth()->lastOfMonth()->toDateString();
                $q->whereBetween('date', [$firstDayOfLastMonth, $lastDayOfLastMonth]);
            });
        }

        if ($request->category_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        if ($request->brand_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }
        if ($request->group_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->invoice_id) {
            $models->whereHas('order', function ($q) use ($request) {
                $q->where('prefix', $request->invoice_id);
            });
        };

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', SalesDetailsResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function grandTotal(Request $request)
    {
        $data = [];
        $models = $this->orderItemModel->filter($request)->where('type', 'sales')
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            });

        if ($request->start_date && $request->end_date) {
            $models->whereHas('order', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        };

        // Filter for today
        if ($request->has('today')) {
            $models->whereHas('order', function ($q) use ($request) {
                $today = Carbon::today()->toDateString();
                $q->whereDate('date', $today);
            });
        }

        // Filter for last 7 days
        if ($request->has('last_7_days')) {
            $models->whereHas('order', function ($q) use ($request) {
                $sevenDaysAgo = Carbon::today()->subDays(7)->toDateString();
                $q->whereDate('date', '>=', $sevenDaysAgo);
            });
        }

        // Filter for last 30 days
        if ($request->has('last_30_days')) {
            $models->whereHas('order', function ($q) use ($request) {
                $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
                $q->whereDate('date', '>=', $thirtyDaysAgo);
            });
        }

        // Filter for last month
        if ($request->has('last_month')) {
            $models->whereHas('order', function ($q) use ($request) {
                $firstDayOfLastMonth = Carbon::today()->subMonth()->firstOfMonth()->toDateString();
                $lastDayOfLastMonth = Carbon::today()->subMonth()->lastOfMonth()->toDateString();
                $q->whereBetween('date', [$firstDayOfLastMonth, $lastDayOfLastMonth]);
            });
        }

        if ($request->category_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        if ($request->brand_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }
        if ($request->group_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->invoice_id) {
            $models->whereHas('order', function ($q) use ($request) {
                $q->where('prefix', $request->invoice_id);
            });
        };

        $models->get();
        $data['quantity'] = $models->sum('quantity');
        $data['sub_total'] = $models->sum('sub_total');

        return $data;
    }

    public function getInvoiceId(Request $request)
    {
        $models = $this->orderModel->filter($request)
            ->where('order_type', 'sales')
            ->where('status', 'done')
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->pluck('prefix')
            ->map(function ($prefix) {
                return (object) ['prefix' => $prefix];
            });

        return $models;
    }
}
