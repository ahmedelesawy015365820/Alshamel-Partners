<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Product;
use Modules\PointOfSale\Entities\ProductVariant;
use Modules\PointOfSale\Transformers\InventoryResource;

class InventoryController extends Controller
{
    public function __construct(private ProductVariant $productVariantModel)
    {
        $this->productVariantModel = $productVariantModel;
    }

    public function all(Request $request)
    {
        $models = $this->productVariantModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        // Filter for today
        if ($request->start_date && $request->end_date) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
            });
        };

        if ($request->branch_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->brand_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }
        if ($request->category_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        if ($request->group_id) {
            $models->whereHas('product', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        } //

        return responseJson(200, 'success', InventoryResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function grandTotal(Request $request)
    {
        $data = [];

        $query = $this->productVariantModel->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->start_date && $request->end_date) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
            });
        }

        if ($request->branch_id) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->brand_id) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }

        if ($request->category_id) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->group_id) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }
        $models = $query->get();

        $data['purchase_price'] = $models->sum('purchase_price');
        $data['selling_price'] = $models->sum('selling_price');
        $totalQuantity = 0;
        foreach ($models as $variant) {
            $totalQuantity += $variant->product->orderItems->sum('quantity');
        }

        $data['quantity'] = $totalQuantity;

        return $data;
    }
}
