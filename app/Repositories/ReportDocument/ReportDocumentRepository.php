<?php

namespace App\Repositories\ReportDocument;

use Illuminate\Support\Facades\DB;
use Modules\RecievablePayable\Entities\RpOpeningBalance;

class ReportDocumentRepository implements ReportDocumentInterface
{
    public function __construct(private \App\Models\DocumentHeader $modelDocumentHeader ,private \App\Models\VoucherHeader $modelVoucherHeader,private RpOpeningBalance $modelOpenningBalance)
    {
        $this->modelDocumentHeader   = $modelDocumentHeader;
        $this->modelVoucherHeader    = $modelVoucherHeader;
        $this->modelOpenningBalance  = $modelOpenningBalance;
    }
    public function allOpeningBalances($request)
    {
        $model_opening = $this->modelOpenningBalance
            ->where('customer_id',$request->customer_id)
            ->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('date', ">=", $request->start_date)
                        ->whereDate('date', "<=", $request->end_date);

                });
            })->get();
        $data_balances = [];
        foreach ($model_opening as $index => $balances){
            $data_balances[$index]['screen'] = "OpenningBalance";
            $data_balances[$index]['id']       = $balances->id;
            $data_balances[$index]['date']     = $balances->date;
            $data_balances[$index]['document_name']   = "OpenningBalance";
            $data_balances[$index]['document_name_e'] = "رصيد افتتاحي";
            $data_balances[$index]['prefix']   = null;
            $data_balances[$index]['debit']    = $balances->debit;
            $data_balances[$index]['credit']   = $balances->credit;
        }

        $balances_total = [];

        $model_total = $this->modelOpenningBalance->select(DB::raw('SUM(debit) as debit'),DB::raw('SUM(credit) as credit'))
            ->where('customer_id',$request->customer_id)
            ->whereDate('date', "<", $request->start_date)->get();

        $balances_total['before '] = "debit_opening";
        $balances_total['debit_before'] = $model_total[0]['debit'] ?? 0;
        $balances_total['credit_before']  = $model_total[0]['credit'] ?? 0;
        $balances_total['total_before']  = $model_total[0]['debit'] - $model_total[0]['credit'] ;

        $array_balances = [];
        $array_balances['data_balances'] = $data_balances;
        $array_balances['balances_total']   = $balances_total;
        return $array_balances;

    }
    public function allDocumentHeader($request)
    {
        $model_document = $this->modelDocumentHeader
            ->where('customer_id',$request->customer_id)
            ->whereHas('document',function ($q){
                $q->where('attributes->customer',"!=","0");
            })
            ->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('date', ">=", $request->start_date)
                        ->whereDate('date', "<=", $request->end_date);

                });
            })->get();
        $data_document = [];
        foreach ($model_document as $index => $documents){
            if($documents->document && $documents->document->attributes){
                $data_document[$index]['screen'] = "DocumentHeader";
                $data_document[$index]['id']     = $documents->id;
                $data_document[$index]['date']     = $documents->date;
                $data_document[$index]['document_name'] = $documents->document->name??null;
                $data_document[$index]['document_name_e'] = $documents->document->name_e??null;
                $data_document[$index]['prefix']   = $documents->prefix;
                $data_document[$index]['debit']    = $documents->document ? $documents->document->attributes['customer'] == "1"   ? $documents->total_invoice : 0  : 0;
                $data_document[$index]['credit']   = $documents->document ? $documents->document->attributes['customer'] == "-1" ? $documents->total_invoice : 0 : 0;
            }
        }

        $document_total = [];
        $model_total = $this->modelDocumentHeader
            ->where('customer_id',$request->customer_id)
            ->whereDate('date', "<", $request->start_date)->get();
        foreach ($model_total as  $documents){
            if($documents->document && $documents->document->attributes){
                $document_total['before '] = "document_header";
                $document_total['debit_before'] = $documents->document ? $documents->document->attributes['customer'] == "1"  ? $documents->total_invoice : 0  : 0;
                $document_total['credit_before']  = $documents->document ? $documents->document->attributes['customer'] == "-1" ? $documents->total_invoice : 0 : 0;
                $document_total['total_before']  = $document_total['debit_before'] - $document_total['credit_before'] ;
            }
        }

        $array_documents = [];
        $array_documents['data_document']    = $data_document;
        $array_documents['document_total']   = $document_total;
        return $array_documents;


    }
    public function allVoucherHeader($request)
    {
        $model_voucher = $this->modelVoucherHeader
            ->where('customer_id',$request->customer_id)
            ->whereHas('document',function ($q){
                $q->where('attributes->customer',"!=","0");
            })
            ->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('date', ">=", $request->start_date)
                        ->whereDate('date', "<=", $request->end_date);

                });
            })->get();
        $data_voucher = [];
        foreach ($model_voucher as $index => $documents){
            if($documents->document && $documents->document->attributes ){
                $data_voucher[$index]['screen']          = "VoucherHeader";
                $data_voucher[$index]['id']              = $documents->id;
                $data_voucher[$index]['date']            = $documents->date;
                $data_voucher[$index]['document_name']   = $documents->document->name??null;
                $data_voucher[$index]['document_name_e'] = $documents->document->name_e??null;
                $data_voucher[$index]['prefix']          = $documents->prefix;
                $data_voucher[$index]['debit']           =  $documents->document->attributes['customer'] == "1"   ? $documents->amount : 0  ;
                $data_voucher[$index]['credit']          =  $documents->document->attributes['customer'] == "-1" ? $documents->amount : 0 ;
                $data_voucher[$index]['balance']         =  0 ;
            }
        }

        $voucher_total = [];
        $model_total = $this->modelVoucherHeader
            ->where('customer_id',$request->customer_id)
            ->whereDate('date', "<", $request->start_date)->get();
        foreach ($model_total as  $vouchers){
            if($vouchers->document && $vouchers->document->attributes){
                $voucher_total['before '] = "document_voucher";
                $voucher_total['debit_before'] = $vouchers->document->attributes['customer'] == "1"   ? $vouchers->amount : 0  ;
                $voucher_total['credit_before']  = $vouchers->document->attributes['customer'] == "-1" ? $vouchers->amount : 0 ;
                $voucher_total['total_before']  = $voucher_total['debit_before'] - $voucher_total['credit_before'] ;
            }
        }

        $array_vouchers = [];
        $array_vouchers['data_voucher']    = $data_voucher;
        $array_vouchers['voucher_total']   = $voucher_total;
        return $array_vouchers;


    }

    public function allCustomerStatementOfAccount($request)
    {
        $Opening_Balances  =  $this->allOpeningBalances($request);

         $Document_Headers  =  $this->allDocumentHeader($request);

         $Voucher_Headers   =  $this->allVoucherHeader($request);
        $collection_data = collect([
            $Opening_Balances['data_balances'],
            $Document_Headers['data_document'],
            $Voucher_Headers['data_voucher'],
        ])->collapse()->sortBy('date')->values()->all();

        $previous_balance = collect([
            $Opening_Balances['balances_total'],
            $Document_Headers['document_total'],
            $Voucher_Headers['voucher_total'],
        ])->sum('total_before');

        foreach ($collection_data as $index=>$data)
        {
            $collection_data[$index]['balance']  = $previous_balance +  $collection_data[$index]['debit'] -  $collection_data[$index]['credit'];
            $previous_balance = $collection_data[$index]['balance'];

        }

        return $collection_data;

    }


}
