<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Order;

class SuppliersSummaryReportController extends Controller
{
    public function __construct(private Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }
    public function all(Request $request)
    {
        $query = $this->orderModel
            ->where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier');

        if ($request->branch_id) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $query->join('general_suppliers', 'pos_orders.supplier_id', '=', 'general_suppliers.id');

        $query->select(DB::raw('general_suppliers.name,SUM(pos_orders.total) as supplier_purchases,SUM(pos_orders.total) as total_payment, SUM(pos_orders.due_amount) as due'))->groupBy('supplier_id');

        $models = $query->paginate($request->per_page);


        return [
            'message' => 'success',
            'data' => $models,
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
    }

    public function grandTotal(Request $request)
    {
        $query = $this->orderModel
            ->where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier');

        if ($request->branch_id) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $models = $query->get();
        $totalPurchases = $models->sum('total');
        $totalPayment = $models->sum('total');
        $totalDue = $models->sum('due_amount');

        $data = [
            'total_purchases' => $totalPurchases,
            'total_payment' => $totalPayment,
            'total_due' => $totalDue,
        ];

        return ['message' => 'success', 'data' => $data];

    }
}
