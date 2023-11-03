<?php

namespace Modules\Booking\Http\Controllers;

use App\Models\DocumentHeader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Unit;

class ReportController extends Controller
{
    public function houseKeeping(Request $request)
    {

        $models = Unit::with(['documentHeaderDetails' => function ($query) use ($request) {
            $query->where('unit_status_id', 16)
                ->whereDate('date_from', '<=', now())
                ->whereDate('date_to', '>=', now())
                ->whereHas('documentHeader', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                })
                ->select('unit_id', 'document_header_id', 'category_booking')
                ->with(['documentHeader' => function ($query) {
                    $query->select('id', 'customer_id')
                        ->with(['customer' => function ($query) {
                            $query->select('id', 'nationality');
                        }]);
                }]);
        }])->select('id', 'code')->get();

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);
    }

    public function checkIn(Request $request)
    {

        $models = DocumentHeader::where('branch_id', $request->branch_id)
            ->where('document_id', 33)
            ->with(['documentHeaderDetails' => function ($query) use ($request) {
                $query->whereDate('date_from', '<=', now())
                    ->whereDate('date_to', '>=', now())
                    ->select('id', 'document_header_id', 'date_from', 'date_to', 'unit_id', 'category_booking', 'rent_days')
                    ->with(['unit' => function ($query) {
                        $query->select('id', 'code');
                    }]);
            }])->with(['customer' => function ($qq) {
            $qq->select('id', 'name', 'name_e', 'nationality')->with(['voucherHeaders' => function ($q) {
                $q->select('id', 'customer_id', 'amount')->whereNull('break_settlement_id');
            }]);
        }])
            ->select('id', 'branch_id', 'document_id', 'customer_id', 'attendans_num', 'invoice_discount', 'net_invoice')
            ->get();

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);

    }
}
