<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\GeneralCustomer;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\Payment;

class PaymentSummaryReportController extends Controller
{
    public function __construct(private Payment $payment, private GeneralCustomer $customerModel, private Order $orderModel)
    {
        $this->payment = $payment;
        $this->customerModel = $customerModel;
        $this->orderModel = $orderModel;
    }

    public function all(Request $request)
    {

        if ($request->has('group') && $request->group === 'date') {
            $query = $this->payment->query();

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            if ($request->order_type) {
                $query->whereHas('order', function ($q) use ($request) {
                    $q->where('order_type', $request->order_type);
                });
            }

            if ($request->payment_method_id) {
                $query->whereHas('paymentMethod', function ($q) use ($request) {
                    $q->where('payment_method_id', $request->payment_method_id);
                });
            }

            $perPage = $request->query('per_page', 10);
            $models = $query->select(DB::raw('id, date, group_concat(payment_method_id) as payment_method_ids, order_id, SUM(paid) as total_paid'))
                ->groupBy('date')
                ->paginate($perPage);

            $orderIds = $models->pluck('order_id')->toArray();
            $paymentMethodIds = array_unique(explode(',', implode(',', $models->pluck('payment_method_ids')->toArray())));

            $orders = $this->orderModel->whereIn('id', $orderIds)->get();
            $paymentMethods = PaymentMethod::whereIn('id', $paymentMethodIds)->get();

            $models->transform(function ($model) use ($orders, $paymentMethods) {
                $order = $orders->firstWhere('id', $model->order_id);

                $paymentMethodIds = array_unique(explode(',', $model->payment_method_ids));
                $paymentMethodNames = $paymentMethods->whereIn('id', $paymentMethodIds)->pluck('name')->toArray();

                $model->type = $order->order_type ?? '';
                $model->method_name = $paymentMethodNames;
                return $model;
            });

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $models->total(),
                'current_page' => $models->currentPage(),
                'next_page_url' => $models->nextPageUrl(),
                'previous_page_url' => $models->previousPageUrl(),
                'last_page' => $models->lastPage(),
                'has_more_pages' => $models->hasMorePages(),
                'from' => $models->firstItem(),
                'to' => $models->lastItem(),
            ];

            $response = [
                'message' => 'success',
                'data' => $models->items(),
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        if ($request->has('group') && $request->group === 'customer') {
            $query = $this->payment->query();

            $query->join('pos_orders', 'pos_payments.order_id', '=', 'pos_orders.id');
            $query->join('general_customers', 'pos_orders.customer_id', '=', 'general_customers.id');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            if ($request->order_type) {
                $query->whereHas('order', function ($q) use ($request) {
                    $q->where('order_type', $request->order_type);
                });
            }

            if ($request->payment_method_id) {
                $query->whereHas('paymentMethod', function ($q) use ($request) {
                    $q->where('payment_method_id', $request->payment_method_id);
                });
            }

            $perPage = $request->query('per_page', 10);
            $models = $query->select([
                'pos_orders.customer_id',
                'pos_orders.id',
                'pos_payments.order_id',
                'general_customers.name as customer_name',
                DB::raw('group_concat(pos_payments.payment_method_id) as payment_method_ids'),
                DB::raw('SUM(pos_payments.paid) as total_paid'),
            ])->groupBy('pos_orders.customer_id')
              ->paginate($perPage);

            $orderIds = $models->pluck('order_id')->toArray();
            $paymentMethodIds = array_unique(explode(',', implode(',', $models->pluck('payment_method_ids')->toArray())));

            $orders = $this->orderModel->whereIn('id', $orderIds)->get();
            $paymentMethods = PaymentMethod::whereIn('id', $paymentMethodIds)->get();

            $models->transform(function ($model) use ($orders, $paymentMethods) {
                $order = $orders->firstWhere('id', $model->order_id);

                $paymentMethodIds = array_unique(explode(',', $model->payment_method_ids));
                $paymentMethodNames = $paymentMethods->whereIn('id', $paymentMethodIds)->pluck('name')->toArray();

                $model->customer_name = $order->customer->name ?? '';
                $model->type = $order->order_type ?? '';
                $model->method_name = $paymentMethodNames;
                return $model;
            });

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $models->total(),
                'current_page' => $models->currentPage(),
                'next_page_url' => $models->nextPageUrl(),
                'previous_page_url' => $models->previousPageUrl(),
                'last_page' => $models->lastPage(),
                'has_more_pages' => $models->hasMorePages(),
                'from' => $models->firstItem(),
                'to' => $models->lastItem(),
            ];

            $response = [
                'message' => 'success',
                'data' => $models->items(),
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

    }

    public function grandTotal(Request $request)
    {
        if ($request->has('group') && $request->group === 'date') {
            $models = $this->payment->get()->groupBy('date');

            if ($request->start_date && $request->end_date) {
                $models = $models->filter(function ($modelCollection, $date) use ($request) {
                    return ($date >= $request->start_date && $date <= $request->end_date);
                });
            }
            if ($request->order_type) {
                $models = $models->filter(function ($modelCollection) use ($request) {
                    return $modelCollection->contains(function ($model) use ($request) {
                        return optional($model->order)->order_type === $request->order_type;
                    });
                });
            }
            if ($request->payment_method_id) {
                $models = $models->filter(function ($modelCollection) use ($request) {
                    return $modelCollection->contains(function ($model) use ($request) {
                        return optional($model->paymentMethod)->payment_method_id === $request->payment_method_id;
                    });
                });
            }

            $totalSum = 0;
            $data = [];

            foreach ($models as $date => $modelCollection) {
                $total = $modelCollection->sum('paid');
                $totalSum += $total;
            }

            $data[] = [
                'total' => $totalSum,
            ];

            return ['message' => 'success', 'data' => $data];
        }

        if ($request->has('group') && $request->group === 'customer') {
            $models = $this->payment->get()->groupBy('customer_id');

            if ($request->start_date && $request->end_date) {
                $models = $models->map(function ($modelCollection) use ($request) {
                    return $modelCollection->filter(function ($model) use ($request) {
                        return ($model->date >= $request->start_date && $model->date <= $request->end_date);
                    });
                });
            }

            if ($request->order_type) {
                $models = $models->map(function ($modelCollection) use ($request) {
                    return $modelCollection->filter(function ($model) use ($request) {
                        return optional($model->order)->order_type === $request->order_type;
                    });
                });
            }

            if ($request->payment_method_id) {
                $models = $models->map(function ($modelCollection) use ($request) {
                    return $modelCollection->filter(function ($model) use ($request) {
                        return optional($model->paymentMethod)->payment_method_id === $request->payment_method_id;
                    });
                });
            }

            $totalSum = 0;
            $data = [];

            foreach ($models as $customerId => $modelCollection) {
                $total = $modelCollection->sum('paid');
                $totalSum += $total;
            }

            $data[] = [
                'total' => $totalSum,
            ];

            return ['message' => 'success', 'data' => $data];
        }

    }

}
