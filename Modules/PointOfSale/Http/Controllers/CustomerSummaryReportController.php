<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Order;

class CustomerSummaryReportController extends Controller
{
    public function __construct(private Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }
    public function all(Request $request)
    {
        $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

        $query->join('general_customers', 'pos_orders.customer_id', '=', 'general_customers.id');

        if ($request->customer_group_id) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('customer_group_id', $request->customer_group_id);
            });
        }
        if ($request->branch_id) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $query->select(
            'general_customers.name',
            'general_customers.name_e',
            'pos_orders.customer_id',
            DB::raw('SUM(CASE WHEN pos_orders.returned_invoice IS NOT NULL THEN pos_orders.total ELSE 0 END) as total_sales'),
            DB::raw('SUM(CASE WHEN pos_orders.return_type IS NOT NULL THEN pos_orders.total ELSE 0 END) as total_returned'),
            DB::raw('SUM(CASE WHEN pos_orders.return_type IS NOT NULL THEN pos_orders.total ELSE 0 END) as total_payment'),
            DB::raw('SUM(pos_orders.due_amount) as total_due')
        )->groupBy('pos_orders.customer_id');

        $models = $query->paginate($request->per_page);

        $responseData = [
            'message' => 'success',
            'data' => $models->items(),
            'pagination' => [
                'per_page' => $models->perPage(),
                'current_page' => $models->currentPage(),
                'total' => $models->total(),
                'last_page' => $models->lastPage(),
                'has_more_pages' => $models->hasMorePages(),
                'from' => $models->firstItem(),
                'to' => $models->lastItem(),
                'path' => $models->path(),
                'next_page_url' => $models->nextPageUrl(),
                'previous_page_url' => $models->previousPageUrl(),
            ],
        ];

        return $responseData;
    }

    public function grandTotal(Request $request)
    {
        $modelsQuery = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

        if ($request->customer_group_id) {
            $modelsQuery->whereHas('customer', function ($q) use ($request) {
                $q->where('customer_group_id', $request->customer_group_id);
            });
        }

        if ($request->branch_id) {
            $modelsQuery->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $totalSales = $modelsQuery->whereNotNull('returned_invoice')->sum('total');
        $totalReturned = $modelsQuery->whereNotNull('return_type')->sum('total');
        $totalPayment = $modelsQuery->whereNotNull('return_type')->sum('total');
        $totalDue = $modelsQuery->sum('due_amount');

        $data = [
            'total_sales' => $totalSales,
            'total_returned' => -$totalReturned,
            'total_payment' => $totalPayment,
            'total_due' => $totalDue,
        ];

        return ['message' => 'success', 'data' => $data];
    }


}
