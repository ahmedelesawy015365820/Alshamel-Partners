<?php

namespace Modules\RealEstate\Repositories\RlstInvoice;

use App\Models\Serial;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstInvoiceItem;
use Modules\RecievablePayable\Entities\RpBreakDown;

class RlstInvoiceRepository implements RlstInvoiceInterface
{

    public function __construct(private RlstInvoice $model,  RlstInvoiceItem $modelInvoiceitem , RpBreakDown $modelBreakDown , Transaction $modelTransaction)
    {
        $this->model = $model;
        $this->modelInvoiceitem = $modelInvoiceitem;
        $this->modelBreakDown   = $modelBreakDown;
        $this->modelTransaction = $modelTransaction;

    }

    // public function all($request)
    // {
    //     $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

    //     if ($request->per_page) {
    //         return ['data' => $models->paginate($request->per_page), 'paginate' => true];
    //     } else {
    //         return ['data' => $models->get(), 'paginate' => false];
    //     }
    // }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->voucher){
            $models->whereHas('transactions');
        }

        if ($request->module_type){
            $models->where('module_type',$request->module_type);
        }

        if ($request->invoice){
            $models->doesnthave('transactions');
        }

        if ($request->per_page) {
            $paginator = $models->paginate($request->per_page);
            $paginator->getCollection()->transform(function ($item) {
                unset($item->deleted_at);
                return $item;
            });
            return ['data' => $paginator, 'paginate' => true];
        } else {
            $models->get()->transform(function ($item) {
                unset($item->deleted_at);
                return $item;
            });
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($request)
    {
        return DB::transaction(function () use ($request) {

            $data = $request->validated();
            $serial = Serial::where([['branch_id',$request->branch_id],['document_id',$request->document_id]])->first();
            $data['serial_id'] = $serial ? $serial->id:null;
            $model = $this->model->create($data);

            if ($request->has('items'))
            {
                $items = $data['items'];
                unset($data['items']);
                $itemData = [];
                foreach ($items as $item) {
                    $itemData[$item['item_id']] = [
                        'quantity' => $item['quantity'],
                        'amount' => $item['amount'],
                    ];
                }
                $model->items()->sync($itemData);
            }

            if ($request['break_downs'] !=  null){

                foreach ($request['transactions'] as $break_down_data ){
                    $break_down =  $this->modelBreakDown->where('id',$break_down_data['break_id'])->first();
                    $break_down->update([
                        'amount_status'=>$break_down_data['amount_status'],
                        'invoice_id'=>$model->id,
                    ]);
                }
                foreach ($request['transactions'] as $transaction):
                    $this->modelTransaction->create(array_merge($transaction,['invoice_id'=>$model->id]));
                endforeach;
            }
            return $model;
        });
    }

    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $data = $request->validated();
            $model = $this->model->find($id);
            $model->update($data);
            if ($request->has('items'))
            {
                $items = $data['items'];
                unset($data['items']);
                $this->modelInvoiceitem->where('invoice_id', $id)->delete();
                $itemData = [];
                foreach ($items as $item) {
                    $itemData[$item['item_id']] = [
                        'quantity' => $item['quantity'],
                        'amount' => $item['amount'],
                    ];
                }
                $model->items()->sync($itemData);
            }

            if ($request['break_downs'] !=  null){
                collect($this->modelBreakDown->where('invoice_id',$model->id)->get())->each(function ($item){
                    $item->update(["invoice_id" => null,'amount_status'=>'Unpaid']);
                });
                foreach ($request['transactions'] as $break_down_data ){
                    $break_down =  $this->modelBreakDown->where('id',$break_down_data['break_id'])->first();
                    $break_down->update(['invoice_id'=>$model->id,'amount_status'=>$break_down_data['amount_status']]);
                }
                $this->modelTransaction->where('invoice_id',$model->id)->delete();
                foreach ($request['transactions'] as $transaction):
                    $this->modelTransaction->create(array_merge($transaction,['invoice_id'=>$model->id]));
                endforeach;
            }

            return $model;
        });
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        if ($model->transactions){
            collect($this->modelBreakDown->where('invoice_id',$model->id)->get())->each(function ($item){
                $item->update(["invoice_id" => null]);
            });
            $this->modelTransaction->where('invoice_id',$model->id)->delete();
        }
        if ($model->breakDowns){
            $this->modelBreakDown->where([['break_id',$model->id],['break_type','invoice']])->delete();
        }

        $model->delete();
    }
}
