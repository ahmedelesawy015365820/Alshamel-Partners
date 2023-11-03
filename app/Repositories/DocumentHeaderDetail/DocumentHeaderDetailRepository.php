<?php


namespace App\Repositories\DocumentHeaderDetail;
use App\Http\Resources\Document\DocumentResource;
use App\Models\GeneralCustomTable;
use Carbon\Traits\Creator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Booking\Entities\Setting;
use Modules\Booking\Entities\Unit;
// use Carbon\Carbon;


class DocumentHeaderDetailRepository implements DocumentHeaderDetailInterface
{
    public function __construct(private \App\Models\DocumentHeaderDetail $model,private Setting $modelSetting, private Unit $modelUnit)
    {
        $this->model = $model;
        $this->modelSetting = $modelSetting;
        $this->modelUnit = $modelUnit;
    }


    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');


        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        $data = $this->model->find($id);
        return $data;
    }


    public function create($request){
        return DB::transaction(function () use ($request) {
            $data = $this->model->create($request);
            return $data;
        });
    }


    public function update($request,$id){
        $data = $this->model->find($id);
        $data->update($request);
        return $data;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id){
        $model = $this->model->find($id);
        $model->delete();
    }

    public function getReportDocument($request)
    {
        $models = $this->model->filter($request)->reportdetail($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->whereDate('date_from'  , '<=',$request->date_to)
                  ->whereDate('date_to'  , '>=',$request->date_from)->with(['governorate','package','category','documentHeader'=>function($q){
                $q->with('customer','externalSalesmen','employee');
            }])->get();

        foreach ($models as $index => $model){

            $line_date_from = new Carbon($model->date_from); // 1-  1-2-2023
            $line_date_to   = new Carbon($model->date_to); // 1-  28-2-2023

            $date_to        = new Carbon($request->date_to);  // 1-  30-1-2023
            $date_from      = new Carbon($request->date_from); // 1-3-2023

            $total_executed_days = 0;
            $executed_days = 0;

            if ($date_from <= $line_date_from && $date_to >= $line_date_to ){
                    //            1-لو الفترة كلها بتقع داخل البحث
                $executed_days         =  $line_date_from->diffInDays($line_date_to);
            } elseif ( $date_from >= $line_date_from && $date_to >= $line_date_to){
                //                2-لوتاريخ بداية البحث اصغر من تاريخ بداية الرووتاريخ نهاية البحث اكبر من تاريخ نهاية الرو = فرق الايام من تاريخ بداية البحث حتى تاريخ نهاية الرو
                $executed_days            =  $date_from->diffInDays($line_date_to) +1;
                $total_executed_days      =  $date_from->diffInDays($line_date_from) -1;

            }elseif ($date_from <= $line_date_from && $date_to <= $line_date_to){
                //                3-لوتاريخ بداية البحث اكبر من تاريخ بداية الرو و تاريخ نهاية البحث اصغر من تاريخ نهاية الرو = فرق الايام من تاريخ بداية الرو حتى تاريخ نهاية البحث
                $executed_days            =  $line_date_from->diffInDays($date_to) +1;
            }elseif($date_from >= $line_date_from && $date_to <= $line_date_to){
                //                - لوتاريخ بداية البحث اصغر من تاريخ بداية الرو و تاريخ نهاية البحث اصغر من تاريخ نهاية الرو=فرق الايام من تاريخ بداية البحث حتى تاريخ نهاية البحث
                    $executed_days            =  $date_from->diffInDays($date_to) + 1;
                    $total_executed_days      =  $line_date_from->diffInDays($date_from) -1;
            }

            $remaining_days                          =  $model->rent_days   - $executed_days - $total_executed_days;
            $previously_used_days                    =  $total_executed_days;

            $model['line_executed_days']             = $executed_days  ;
            $model['line_remaining_days']            = $remaining_days  ;
            $model['line_previously_used_days']      = $previously_used_days;
            // net invoice
            $model['line_unrealized_revenue']        = $remaining_days ? ($model->net_invoice * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_revenue']     = $previously_used_days ? $model->net_invoice * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_revenue']           = $executed_days ? $model->net_invoice * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_revenue']          = $remaining_days ? $model->net_invoice * ($remaining_days / $model->rent_days) : 0;
            // sell Method Discount
            $model['line_unrealized_sell_method_discount']        = $remaining_days ? ($model->sell_method_discount * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_sell_method_discount']     = $previously_used_days ? $model->sell_method_discount * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_sell_method_discount']           = $executed_days ? $model->sell_method_discount * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_sell_method_discount']          = $remaining_days ? $model->sell_method_discount * ($remaining_days / $model->rent_days) : 0;
            // External Commission
            $model['line_unrealized_external_commission']        = $remaining_days ? ($model->external_commission * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_external_commission']     = $previously_used_days ? $model->external_commission * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_external_commission']           = $executed_days ? $model->external_commission * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_external_commission']          = $remaining_days ? $model->external_commission * ($remaining_days / $model->rent_days) : 0;
            // Commission
            $model['line_unrealized_commission']     = 0;
            $model['line_used_previous_commission']  = 0;
            $model['line_current_commission']        = 0;
            $model['line_remaning_commission']       = 0;

            $model['line_total_debit_note']          = 0;
            $model['line_total_returns']             = 0;
            $model['line_payments']                  = 0;
        }

        return $models;


    }

    public function getAnnualFinancialReport($request)
    {
        $data  = [];
        $index = 0;
        $year = $request['year'];
        for ($i = 1; $i < 13; $i++) {
           $first_day = $year.'-'.$i.'-'."1";
           $date_from = new Carbon($first_day);

            $calc_finance = $this->calcFinance($date_from->toDateString(),$date_from->endOfMonth()->toDateString(),$request);
//            return $calc_finance ;
            $data[$index]['month'] = $i;
            $data[$index]['total_month']                 = $calc_finance->where('is_sum',true)->sum('total');
            $data[$index]['net_invoice_month']           = $calc_finance->where('is_sum',true)->sum('net_invoice');
            $data[$index]['sell_method_discount_month']  = $calc_finance->where('is_sum',true)->sum('sell_method_discount');
            $data[$index]['external_commission_month']   = $calc_finance->where('is_sum',true)->sum('external_commission');
            $data[$index]['invoice_discount_month']      = $calc_finance->where('is_sum',true)->sum('invoice_discount');
            //days
            $data[$index]['rent_days_month']      = $calc_finance->sum('rent_days');
            $data[$index]['executed_days_month']    = $calc_finance->sum('line_executed_days');
            $data[$index]['remaining_days_month'] = $calc_finance->sum('line_remaining_days');
            $data[$index]['previously_used_days_month']       = $calc_finance->sum('line_previously_used_days');

            // revenue
            $data[$index]['unrealized_revenue_month']    = $calc_finance->sum('line_unrealized_revenue');
            $data[$index]['used_previous_revenue_month'] = $calc_finance->sum('line_used_previous_revenue');
            $data[$index]['current_revenue_month']       = $calc_finance->sum('line_current_revenue');
            $data[$index]['remaning_revenue_month']      = $calc_finance->sum('line_remaning_revenue');

            // sell Method Discount
            $data[$index]['unrealized_sell_method_discount_month']    = $calc_finance->sum('line_unrealized_sell_method_discount');
            $data[$index]['previous_sell_method_discount_month'] = $calc_finance->sum('line_used_previous_sell_method_discount');
            $data[$index]['current_sell_method_discount_month']       = $calc_finance->sum('line_current_sell_method_discount');
            $data[$index]['remaning_sell_method_discount_month']      = $calc_finance->sum('line_remaning_sell_method_discount');

            // external commission
            $data[$index]['unrealized_external_commission_month']    = $calc_finance->sum('line_unrealized_external_commission');
            $data[$index]['previous_external_commission_month'] = $calc_finance->sum('line_used_previous_external_commission');
            $data[$index]['current_external_commission_month']       = $calc_finance->sum('line_current_external_commission');
            $data[$index]['remaning_external_commission_month']      = $calc_finance->sum('line_remaning_external_commission');

            // commission
            $data[$index]['unrealized_commission_month'] = $calc_finance->sum('line_unrealized_commission');
            $data[$index]['used_previous_commission_month'] = $calc_finance->sum('line_used_previous_commission');
            $data[$index]['current_commission_month']    = $calc_finance->sum('line_current_commission');
            $data[$index]['remaning_commission_month']   = $calc_finance->sum('line_remaning_commission');

            $data[$index]['total_debit_note_month']      = $calc_finance->sum('line_total_debit_note');
            $data[$index]['total_returns_month']         = $calc_finance->sum('line_total_returns');
            $data[$index]['payments_month']              = $calc_finance->sum('line_payments');
            $index++;

        }
            return $data;

    }

    public function getAnnualFinancialReportDetail($request)
    {
        $year = $request['year'];
        $first_day = $year.'-'.$request->month.'-'."1";
        $date_from = new Carbon($first_day);
        $calc_finance = $this->calcFinance($date_from->toDateString(),$date_from->endOfMonth()->toDateString(),$request);
        return $calc_finance;

    }


    public function calcFinance($date_from,$date_to,$request)
    {
        $models =  $this->model->where(function ($q) use ($date_from,$date_to){
            $q->whereDate('date_from'  , '<=',$date_to)
                ->whereDate('date_to'  , '>=',$date_from);
        })->orWhereHas('documentHeader',function ($q) use ($date_from,$date_to){
            $q->whereDate('date','>=',$date_from)
             ->whereDate('date','<=',$date_to);
        })->with(['governorate','package','category','documentHeader'=>function($q){
            $q->with('customer','externalSalesmen','employee');
        }])->report($request)->get();


        foreach ($models as $index => $model){
            $line_date_from = new Carbon($model->date_from);
            $line_date_to   = new Carbon($model->date_to);
            $date_to        = new Carbon($date_to);
            $date_from      = new Carbon($date_from);
            $date_document_header     = new Carbon($model->documentHeader->date);
            $total_executed_days = 0;
            $executed_days = 0;
            $is_sum = true;

            if ($line_date_from->format('y-m') == $date_to->format('y-m') && $line_date_to->format('y-m') != $date_to->format('y-m')){
                // هنجيب الفرق من التاريخ بداية السطر الي تاريخ نهاية المطلوب
                $executed_days         =  $line_date_from->diffInDays($date_to) +1;
                $is_sum = false;

            } elseif ( $line_date_to->format('y-m') == $date_to->format('y-m') && $line_date_from->format('y-m') != $date_to->format('y-m')){
                // هنجيب الفرق بين تاريخ البداية المطلوب الي تاريخ نهاية  السطر
                $executed_days            =  $date_from->diffInDays($line_date_to) +1;
                $total_executed_days      =  $date_from->diffInDays($line_date_from) -1;
                $is_sum = false;

            }elseif ($line_date_from->format('y-m') == $date_from->format('y-m') &&  $line_date_to->format('y-m') == $date_to->format('y-m')){
                // هنجيب الفرق من التاريخ بداية السطر الي تاريخ نهاية السطر
                $executed_days            =  $line_date_from->diffInDays($line_date_to) ;
                $is_sum = false;

            }elseif($line_date_from->format('y-m') != $date_from->format('y-m') &&  $line_date_to->format('y-m') != $date_to->format('y-m')){

                // هنجيب الفرق من التاريخ بداية المطلوب الي تاريخ نهاية المطلوب + 1
                if ($date_document_header->format('y-m') != $date_from->format('y-m'))
                {
                    $executed_days            =  $date_from->diffInDays($date_to) + 1;
                    $total_executed_days      =  $line_date_from->diffInDays($date_from) -1;
                    $is_sum = false;
                }
            }

            $remaining_days                          =  $model->rent_days   - $executed_days - $total_executed_days;
            $previously_used_days                    =  $total_executed_days;

            $model['line_executed_days']             = $executed_days  ;
            $model['line_remaining_days']            = $remaining_days  ;
            $model['line_previously_used_days']      = $previously_used_days;
            // net invoice
            $model['line_unrealized_revenue']        = $remaining_days ? ($model->net_invoice * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_revenue']     = $previously_used_days ? $model->net_invoice * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_revenue']           = $executed_days ? $model->net_invoice * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_revenue']          = $remaining_days ? $model->net_invoice * ($remaining_days / $model->rent_days) : 0;
            // sell Method Discount
            $model['line_unrealized_sell_method_discount']        = $remaining_days ? ($model->sell_method_discount * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_sell_method_discount']     = $previously_used_days ? $model->sell_method_discount * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_sell_method_discount']           = $executed_days ? $model->sell_method_discount * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_sell_method_discount']          = $remaining_days ? $model->sell_method_discount * ($remaining_days / $model->rent_days) : 0;
            // External Commission
            $model['line_unrealized_external_commission']        = $remaining_days ? ($model->external_commission * $remaining_days) / $model->rent_days : 0;
            $model['line_used_previous_external_commission']     = $previously_used_days ? $model->external_commission * ($previously_used_days / $model->rent_days) : 0;
            $model['line_current_external_commission']           = $executed_days ? $model->external_commission * ($executed_days / $model->rent_days) : 0;
            $model['line_remaning_external_commission']          = $remaining_days ? $model->external_commission * ($remaining_days / $model->rent_days) : 0;
            // Commission
            $model['line_unrealized_commission']     = 0;
            $model['line_used_previous_commission']  = 0;
            $model['line_current_commission']        = 0;
            $model['line_remaning_commission']       = 0;

            $model['line_total_debit_note']          = 0;
            $model['line_total_returns']             = 0;
            $model['line_payments']                  = 0;
            $model['is_sum']                         = $is_sum;
        }

        return $models;
    }


    public function getPanelUsageRateReport($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->whereHas('documentHeader',function ($q){
                $q->whereHas('document',function ($q){
                    $q->where('attributes->item_quantity',"-1");
                });
            })->whereRelation('documentHeader',function ($q) use ($request){
                $q->whereRelation('document' ,function ($q) use ($request){
                    $q->where('document_detail_type',$request->document_detail_type);
                });
            })
            ->where(function ($q) use ($request){
              $q->when($request->governorate_id,function ($q) use ($request){
                $q->where('governorate_id',$request->governorate_id);
              });
            })
            ->where(function ($q) use ($request){
                $q->when($request->category_id,function ($q) use ($request){
                    $q->where('category_id',$request->category_id);
                });
            })->whereYear('date_from',$request->year)->whereYear('date_to',$request->year)
            ->whereMonth('date_from'  , '<=',$request->month)
            ->whereMonth('date_to'  , '>=',$request->month)->get();


        return $models;
    }

    public function getRooms($request)
    {
        $models = $this->modelUnit->with('documentHeaderDetails');

        if ($request->date_from && $request->date_to) {
            $models->where(function ($q) use ($request){
                $q->whereDoesntHave('documentHeaderDetails');
            })->orWhere(function ($q) use ($request){
                $q->whereDoesntHave('documentHeaderDetails', function ($q) use ($request) {
                    $q->where(function ($q) use ($request){
                        $q->whereDate('date_from', '>=', $request->date_from)
                            ->whereDate('date_from', '<=', $request->date_to);
                    })->orWhere(function ($q) use ($request){
                        $q->whereDate('date_to', '>=', $request->date_from)
                            ->whereDate('date_to', '<=', $request->date_to);
                    });
                });
            })->orWhere(function ($q) use($request){
                $q->whereRelation('documentHeaderDetails', function ($q) use ($request) {
                    $q->whereRelation('documentHeader' ,function ($q) use ($request){
                        $q->where('complete_status','UnDelivered');
                    })->where(function ($q) use ($request){
                        $q->whereDate('date_from', '>=', $request->date_from)
                            ->whereDate('date_from', '<=', $request->date_to);
                    })->orWhere(function ($q) use ($request){
                        $q->whereDate('date_to', '>=', $request->date_from)
                            ->whereDate('date_to', '<=', $request->date_to);
                    });
                });
            });
        }

        if ($request->per_page) {
            return  ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return  ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function allBookingReport($request)
    {
        $models =  $this->model->with('item','unit','documentHeader.customer')
           ->where(function ($q) use ($request){
            $q->whereDate('date_from', '>=', $request->date_from)
                ->whereDate('date_from', '<=', $request->date_to)
                ->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('document_id',$request->document_id);
                })->when($request->customer_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('customer_id',$request->customer_id);
                    });
                });
        })->orWhere(function ($q) use ($request){
            $q->whereDate('date_to', '>=', $request->date_from)
                ->whereDate('date_to', '<=', $request->date_to)
                ->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('document_id',$request->document_id);
                })->when($request->customer_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('customer_id',$request->customer_id);
                    });
                });
        });
        if ($request->per_page) {
            return  ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return  ['data' => $models->get(), 'paginate' => false];
        }

    }

}
