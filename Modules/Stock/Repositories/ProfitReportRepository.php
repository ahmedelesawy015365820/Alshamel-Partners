<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\StockSalePurchaseDetail;

class ProfitReportRepository implements ProfitReportInterface
{
    private $model;
    public function __construct(StockSalePurchaseDetail $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request);
        // $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->has('start_date')  && $request->has('end_date')) {
            $models->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->has('stock_id')  && !empty($request->stock_id)) {
            $models->where('stock_id', $request->stock_id);
        }
        if ($request->has('wallet_id')  && !empty($request->wallet_id)) {
            $models->where('wallet_id', $request->wallet_id);
        }
        // dd($request);
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    private function forget($id)
    {
        $keys = [
            "currency",
            "currency_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
