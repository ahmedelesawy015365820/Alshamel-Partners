<?php

namespace App\Repositories\Translation;

use App\Models\Serial;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationInterface
{

    public function __construct(private Transaction $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->invoice_id) {
            $models->where('invoice_id', $request->invoice_id);
        }
        if ($request->module_type)
        {
            $models->where('module_type',$request->module_type);
        }
        if ($request->sponsor == 0)
        {
            $models->whereNull('sponsor_id');
        }
        if ($request->sponsor == 1)
        {
            $models->whereNotNull('sponsor_id');
        }
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

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request['transactions'] as $transaction):
                if ($transaction['module_type'] == 'club' && !$transaction['serial_id'])
                {
                    $serial = Serial::where([['branch_id',$transaction['branch_id']],['document_id',$transaction['document_id']]])->first();
                    $transaction['serial_id'] = $serial ? $serial->id:null;
                }
                $model= $this->model->create($transaction);
                if ($transaction['serial_id'])
                {
                    $serials = generalSerialWithIdCreate($model,$transaction['serial_id']);
                }else{
                    $serials = generalSerial($model);
                }
                $model->update([
                    "serial_number" => $serials['serial_number'],
                    "prefix" => $serials['prefix'],
                ]);
            endforeach;
        });
    }

    public function update($request, $id)
    {
        $model = $this->model->find($id);
        $model->update($request);
        return $model;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        $model->delete();
    }
}
