<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Models\BreakSettlement;
use App\Models\Currency;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentType;
use Modules\RecievablePayable\Http\Requests\CreateBreakDownRequest;
use Modules\RecievablePayable\Transformers\BreakDownMoneyResource;
use Modules\RecievablePayable\Transformers\BreakDownResource;
use Modules\RecievablePayable\Transformers\CustomerStatementResource;
use Modules\RecievablePayable\Transformers\FilterBreakResource;

class RpBreakDownController extends Controller
{
    public function __construct(private RpBreakDown $model)
    {
        $this->model = $model;
    }

    public function show($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new BreakDownResource($model));
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy('instalment_date', 'ASC');
        if ($request->break_type){
            $models->where('break_type',$request->break_type);
        }
        if ($request->break_id && $request->break_type == "openingBalance"){
            $models->whereHas('openingBalance',function ($q) use ($request){
                $q->where('id',$request->break_id)->where('type_document',null);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "invoice"){
            $models->whereHas('invoice',function ($q) use ($request){
                $q->where('id',$request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "contract"){
            $models->whereHas('contract',function ($q) use ($request) {
                $q->where('id', $request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "reservation"){
            $models->whereHas('reservation',function ($q) use ($request) {
                $q->where('id', $request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "documentHeader"){
            $models->whereHas('documentHeader',function ($q) use ($request) {
                $q->where('id', $request->break_id);
            })->where('parent_id',null);
        }

        if ($request->break_id && $request->break_type == "debitNote"){
            $models->whereHas('openingBalance',function ($q) use ($request){
                $q->where('id',$request->break_id)->where('type_document','debitNote');
            })->where('parent_id',null);
        }

        if ($request->parent_id){
            $models->where('parent_id',$request->parent_id);
        }
        if ($request->customer_id){
            $models->where('customer_id',$request->customer_id)->doesnthave('children')
                ->where('invoice_id',null)->orderBy('instalment_date', 'DESC');
        }
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', BreakDownResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateBreakDownRequest $request)
    {
        $data = [];

        foreach ($request->validated()['break_downs'] as  $break_downs ):

            if ($break_downs['repate'] == 1){

                $model = $this->model->create($break_downs);
                $data[] = $model;

            }else{
                $data[] = $this->createObjactBreakDown($break_downs);
            }

        endforeach;

        return collect($data)->collapse();
    }

    public function CalculateDate($break_downs)
    {
        $cont_date = 0;

        $PaymentType = RpInstallmentPaymentType::find($break_downs['instalment_type_id']);
        // بنجمع عدد الايام
        $cont_date  +=  $PaymentType->freq_period ;

        $date_instalment =  \Carbon\Carbon::parse($break_downs['instalment_date']);

        if($PaymentType->step == "D"){
            return $date = $date_instalment->addDays($cont_date)->format('Y-m-d');
        }

        if ($PaymentType->step == "M"){
            return  $date = $date_instalment->addMonths($cont_date)->format('Y-m-d');
        }

        if ($PaymentType->step == "Y"){
            return  $date = $date_instalment->addYears($cont_date)->format('Y-m-d');
        }


    }

    public function createObjactBreakDown($break_downs){

        $currency = Currency::find($break_downs["currency_id"]);


        $sum_total = 0;
        $cont_date = 0;

        $repate =  $break_downs['repate'] - 1;

        if ($break_downs['repate'] == 1){

            $model = $this->model->create($break_downs);
            $data = $model;

        }else{


            $i = 0;
            for ($i;$i < $break_downs['repate'] ;$i++ ){

                $total_break =  $break_downs['total'] / $break_downs['repate'] ;


                if ($i != 0){

                    $round =  round($total_break,$currency->fraction_no) ;
                    $sum_total += $round;

                    if ($i == $repate){
                        $total_amount =  $break_downs['total'] - $sum_total;
                        $total =   round($total_amount,$currency->fraction_no);
                    }else{
                        $total =  $round;

                    }
//
//                    if ($repate)
//                    {
//                        return   $date = $this->CalculateDate($break_downs);
//
//                    }

                    $PaymentType = RpInstallmentPaymentType::find($break_downs['instalment_type_id']);
                    // بنجمع عدد الايام
                    $cont_date  +=  $PaymentType->freq_period ;

                    $date_instalment =  \Carbon\Carbon::parse($break_downs['instalment_date']);

                    if($PaymentType->step == "D"){
                         $date = $date_instalment->addDays($cont_date)->format('Y-m-d');
                    }

                    if ($PaymentType->step == "M"){
                          $date = $date_instalment->addMonths($cont_date)->format('Y-m-d');
                    }

                    if ($PaymentType->step == "Y"){
                          $date = $date_instalment->addYears($cont_date)->format('Y-m-d');
                    }



                    $data_break = collect($break_downs)->except('instalment_date','id','total','repate','debit','credit');

                    $model = $this->model->create(array_merge($data_break->all(),[
                        'instalment_date' => $date,
                        'total' => $total,
                        'debit' => $break_downs['debit']   != 0 ? $total : 0 ,
                        'credit' => $break_downs['credit'] != 0 ? $total : 0 ,
                        'repate' => 1,
                    ]));
                    $data[$i] = $model;

                }else{

                    $data_break = collect($break_downs)->except('id','total','repate','credit','debit');
                    $total = round($total_break,$currency->fraction_no) ;

                    $model = $this->model->create(array_merge($data_break->all(),[
                        'total' => $total,
                        'debit' => $break_downs['debit']   != 0 ? $total : 0 ,
                        'credit' => $break_downs['credit'] != 0 ? $total : 0 ,
                        'repate' => 1,
                    ]));
                        $data[] = $model;

                }


            }

        }
        return $data;
    }


    public function updateBreak(CreateBreakDownRequest $request)
    {
         $date_downs = $request->validated()['break_downs'];
         $this->model->doesnthave('breakSettlements')
            ->where('break_id',$date_downs[0]['break_id'])
          ->where('customer_id',$date_downs[0]['customer_id'])
          ->where('break_type',$date_downs[0]['break_type'])->delete();

        $data = [];
        foreach ($request->validated()['break_downs'] as $index => $break_downs ):
            $model = $this->model->where('id',$break_downs['id'])->first();
            if (!$model){
                $data[] =  $this->createObjactBreakDown($break_downs,$index);
            }
        endforeach;
        return  $data;

    }

    public function destroy($id)
    {
        $model = $this->model->find($id);
        if ($model->hisChildren()){
            return responseJson(400, 'some items has relation cant delete');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function filterBreak(Request $request)
    {

        $models = $this->model->filter($request)->filterbreak($request)->doesnthave('children')->orderBy('instalment_date', 'ASC');
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', FilterBreakResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function getCustomerStatementOfAccount(Request $request)
    {
        $models = $this->model->whereHas('document',function ($q){
            $q->where('attributes->customer','!=',"0");
        })->where(function ($q) use ($request) {
            $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereDate('instalment_date', ">=", $request->start_date)
                    ->whereDate('instalment_date', "<=", $request->end_date);
            });
        })->doesnthave('children')->where('customer_id',$request->customer_id)->orderBy('instalment_date', 'ASC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', CustomerStatementResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getDocumentWithMoneyDetails(Request $request)
    {
        $models = $this->model->data()->whereHas('document',function ($q){
            $q->where('attributes->customer',"1");
        })->doesnthave('children')->where('customer_id',$request->customer_id)
            ->addSelect(['balance' => BreakSettlement::selectRaw('sum(amount) as total_sum')
            ->whereColumn('break_id', 'rp_break_downs.id')
        ])->orderBy('instalment_date', 'ASC');

        if (isset($request->with_paid) && $request->with_paid == 'false')
        {
            $models->having(DB::raw('rp_break_downs.total'),'!=',DB::raw('IFNULL(balance,0)'));
        }
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', BreakDownMoneyResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function getBreakCustomerMoneyDetails(Request $request)
    {
        return $request;

    }


}
