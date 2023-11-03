<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;

class SalesPurchasesReportController extends Controller
{
    public function __construct(private Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }

    public function all(Request $request)
    {
        $response = [];

        $sales = $this->orderModel->where('order_type', 'sales')->where('status', 'done')->where('type', 'customer')->where('returned_invoice', null)->sum('total');
        $sales_return = $this->orderModel->where('order_type', 'sales')->where('status', 'done')->where('type', 'customer')->whereNotNull('returned_invoice')->sum('total');
        $response[] = ["type" => "sales", "total" => $sales];
        $response[] = ["type" => "sales_return", "total" => $sales_return];

        $sales_due = $this->orderModel->where('order_type', 'sales')->where('status', 'done')->where('type', 'customer')->sum('due_amount');
        $sales_tax = $this->orderModel->where('order_type', 'sales')->where('status', 'done')->where('type', 'customer')->sum('total_tax');
        $response[] = ["type" => "sales_due", "total" => $sales_due];
        $response[] = ["type" => "sales_tax", "total" => $sales_tax];

        $purchases = $this->orderModel->where('order_type', 'receiving')->where('status', 'done')->where('type', 'supplier')->where('returned_invoice', null)->sum('total');
        $purchases_return = $this->orderModel->where('order_type', 'receiving')->where('status', 'done')->where('type', 'supplier')->whereNotNull('returned_invoice')->sum('total');
        $response[] = ["type" => "purchases", "total" => $purchases];
        $response[] = ["type" => "purchases_return", "total" => $purchases_return];

        $purchases_due = $this->orderModel->where('order_type', 'receiving')->where('status', 'done')->where('type', 'supplier')->sum('due_amount');
        $purchases_tax = $this->orderModel->where('order_type', 'receiving')->where('status', 'done')->where('type', 'supplier')->sum('total_tax');
        $response[] = ["type" => "purchases_due", "total" => $purchases_due];
        $response[] = ["type" => "purchases_tax", "total" => $purchases_tax];

        return responseJson(200, $response);
    }


}
