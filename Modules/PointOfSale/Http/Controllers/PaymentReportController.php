<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Transformers\PaymentReportResource;

class PaymentReportController extends Controller
{
    public function __construct(private Payment $payment)
    {
        $this->payment = $payment;
    }

    public function all(Request $request)
    {
        $models = $this->payment->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        // Filter for today
        if ($request->start_date && $request->end_date) {
            $models->whereBetween('date', [$request->start_date, $request->end_date]);
        };

        if ($request->payment_method_id) {
            $models->whereHas('paymentMethod', function ($q) use ($request) {
                $q->where('payment_method_id', $request->payment_method_id);
            });
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        } //

        return responseJson(200, 'success', PaymentReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function grandTotal(Request $request)
    {
        $data = [];
        $query = $this->payment->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        };

        if ($request->payment_method_id) {
            $query->whereHas('paymentMethod', function ($q) use ($request) {
                $q->where('payment_method_id', $request->payment_method_id);
            });
        }

        $models = $query->get();
        $data['amount'] = $models->sum('paid');

        return $data;
    }
}
