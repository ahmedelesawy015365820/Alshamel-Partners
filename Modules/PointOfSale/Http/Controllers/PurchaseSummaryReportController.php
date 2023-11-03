<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GeneralCustomer;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Product;
use Modules\PointOfSale\Transformers\PurchaseSummaryReportResource;

class PurchaseSummaryReportController extends Controller
{
    public function __construct(private OrderItem $orderItemModel, private Order $orderModel, private Brand $brandModel, private Category $categoryModel, private Group $groupModel, private Product $productModel, private GeneralCustomer $customerModel)
    {
        $this->orderItemModel = $orderItemModel;
        $this->orderModel = $orderModel;
        $this->brandModel = $brandModel;
        $this->categoryModel = $categoryModel;
        $this->groupModel = $groupModel;
        $this->productModel = $productModel;
        $this->customerModel = $customerModel;
    }

    public function all(Request $request)
    {

        if ($request->has('group') && $request->group === 'brand') {

            $models = $this->brandModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

            if ($request->branch_id) {
                $models->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }
            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'receiving')
                            ->where('status', 'done')
                            ->where('type', 'supplier')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $models->whereIn('id', $productIds);
            }
        }

        // // if ($request->has('category'))
        if ($request->has('group') && $request->group === 'category') {

            $models = $this->categoryModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

            if ($request->branch_id) {
                $models->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            }

            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $models->whereIn('id', $productIds);
            }

        }

        // // if ($request->has('group'))
        if ($request->has('group') && $request->group === 'group') {

            $models = $this->groupModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

            if ($request->branch_id) {
                $models->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            }

            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $models->whereIn('id', $productIds);
            }

        }
        // // if ($request->has('product'))
        if ($request->has('group') && $request->group === 'product') {

            $models = $this->productModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
            if ($request->branch_id) {
                    $models->where('branch_id', $request->branch_id);
            }

            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $models->whereIn('id', $productIds);
            }

        }
        // // if ($request->has('customer'))
        if ($request->has('group') && $request->group === 'customer') {

            $models = $this->customerModel->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

            if ($request->branch_id) {
                $models->whereHas('orders', function ($q) use ($request) {
                    $q->whereHas('items', function ($q) use ($request) {
                        $q->whereHas('product', function ($q) use ($request) {
                            $q->where('branch_id', $request->branch_id);
                        });
                    });
                });

            }

            if ($request->start_date && $request->end_date) {
                $customerIds = $this->orderModel->where('order_type', 'sales')
                    ->where('status', 'done')
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->pluck('customer_id');

                $models->whereIn('id', $customerIds);
            }

        }

        if ($request->has('group') && $request->group === 'date') {

            return $this->GroupByDate($request);

        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', PurchaseSummaryReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function grandTotal(Request $request)
    {
        // if ($request->has('brand'))
        if ($request->has('group') && $request->group === 'brand') {
            $data = [];
            $query = $this->brandModel;
            if ($request->branch_id) {
                $query->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            }
            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'receiving')
                            ->where('status', 'done')
                            ->where('type', 'supplier')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }

            $models = $query->get();
            $data['item_purchased'] = $models->sum('item_purchased');
            $data['total_purchased'] = $models->sum('total_purchased');

            return $data;
        }


        if ($request->has('group') && $request->group === 'category') {
            $data = [];
            $query = $this->categoryModel;
            if ($request->branch_id) {
                $query->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            }
            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }
            $models = $query->get();
            $data['item_purchased'] = $models->sum('item_purchased');
            $data['total_purchased'] = $models->sum('total_purchased');

            return $data;

        }

        if ($request->has('group') && $request->group === 'group') {
            $data = [];
            $query = $this->groupModel;
            if ($request->branch_id) {
                $query->whereHas('products', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });

            }
            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }
            $models = $query->get();
            $data['item_purchased'] = $models->sum('item_purchased');
            $data['total_purchased'] = $models->sum('total_purchased');

            return $data;

        }

        if ($request->has('group') && $request->group === 'product') {
            $data = [];
            $query = $this->productModel;
            if ($request->branch_id) {
                    $query->where('branch_id', $request->branch_id);

            }
            if ($request->start_date && $request->end_date) {
                $productIds = $this->orderItemModel->where('type', 'sales')
                    ->whereHas('order', function ($q) use ($request) {
                        $q->where('order_type', 'receiving')
                            ->where('status', 'done')
                            ->where('type', 'supplier')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }

            $models = $query->get();

            $data['item_purchased'] = $models->sum('item_purchased');
            $data['total_purchased'] = $models->sum('total_purchased');

            return $data;

        }

        if ($request->has('group') && $request->group === 'customer') {
            $data = [];
            $query = $this->customerModel;
            if ($request->branch_id) {
                $query->whereHas('orders', function ($q) use ($request) {
                    $q->whereHas('items', function ($q) use ($request) {
                        $q->whereHas('product', function ($q) use ($request) {
                            $q->where('branch_id', $request->branch_id);
                        });
                    });
                });
            }
            if ($request->start_date && $request->end_date) {
                $customerIds = $this->orderModel->where('order_type', 'receiving')
                    ->where('status', 'done')
                    ->where('type', 'supplier')
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->pluck('customer_id');

                $query->whereIn('id', $customerIds);
            }
            $models = $query->get();

            $data['item_purchased'] = $models->sum('item_purchased');
            $data['total_purchased'] = $models->sum('total_purchased');

            return $data;
        }
        // if ($request->has('date'))
        if ($request->has('group') && $request->group === 'date') {

            $data = $this->GroupByDate($request);

            $summary = [
                'item_purchased' => collect($data['data'])->sum('item_purchased'),
                'total_purchased' => collect($data['data'])->sum('total_purchased'),
            ];

            return $summary;
        }

    }

    public function GroupByDate(Request $request)
    {

        $query = $this->orderModel->where('pos_orders.order_type', 'receiving')
            ->where('pos_orders.status', 'done')
            ->where('pos_orders.type', 'supplier')->whereHas('items', function ($q) {
            $q->where('pos_order_items.type', 'receiving');
        });

        $query->join('pos_order_items', 'pos_orders.id', '=', 'pos_order_items.order_id');

        $query->select(DB::raw('pos_orders.id, pos_orders.date, SUM(pos_orders.total) as total, pos_order_items.quantity'))
            ->groupBy('pos_orders.date');

        $models = $query->paginate($request->per_page);

        return [
            'message' => 'success',
            'data' => $models,
            'pagination' => [
                'per_page' => $models->perPage(),
                'current_page' => $models->currentPage(),
                'total' => $models->total(),
                'last_page' => $models->lastPage(),
                'has_more_pages' => $models->hasMorePages(),
                'from' => $models->firstItem(),
                'to' => $models->lastItem(),
                'path' => $models->path(),
                'next_page_url' => $models->nextPageUrl(),
                'previous_page_url' => $models->previousPageUrl(),
            ],
        ];
    }
}
