<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GeneralCustomer;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Product;

class SalesSummaryController extends Controller
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
            $models = $this->brandModel->filter($request)->orderBy($request->order ?? 'updated_at', $request->sort ?? 'DESC');

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

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = $models->count();
            $totalPages = ceil($totalItems / $perPage);

            $paginatedModels = $models->forPage($currentPage, $perPage)->get();

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ];

            $response = [
                'message' => 'success',
                'data' => $paginatedModels,
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        if ($request->has('group') && $request->group === 'category') {
            $models = $this->categoryModel->filter($request)->orderBy($request->order ?? 'updated_at', $request->sort ?? 'DESC');

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

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = $models->count();
            $totalPages = ceil($totalItems / $perPage);

            $paginatedModels = $models->forPage($currentPage, $perPage)->get();

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ];

            $response = [
                'message' => 'success',
                'data' => $paginatedModels,
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        if ($request->has('group') && $request->group === 'group') {
            $models = $this->groupModel->filter($request)->orderBy($request->order ?? 'updated_at', $request->sort ?? 'DESC');

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

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = $models->count();
            $totalPages = ceil($totalItems / $perPage);

            $paginatedModels = $models->forPage($currentPage, $perPage)->get();

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ];

            $response = [
                'message' => 'success',
                'data' => $paginatedModels,
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        if ($request->has('group') && $request->group === 'product') {
            $models = $this->productModel->filter($request)->orderBy($request->order ?? 'updated_at', $request->sort ?? 'DESC');

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

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = $models->count();
            $totalPages = ceil($totalItems / $perPage);

            $paginatedModels = $models->forPage($currentPage, $perPage)->get();

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ];

            $response = [
                'message' => 'success',
                'data' => $paginatedModels,
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        if ($request->has('group') && $request->group === 'customer') {
            $models = $this->customerModel->filter($request)->orderBy($request->order ?? 'updated_at', $request->sort ?? 'DESC');

            if ($request->branch_id) {
                $models->whereHas('orders.items.product', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            if ($request->start_date && $request->end_date) {
                $customerIds = $this->orderModel->where('order_type', 'sales')
                    ->where('status', 'done')
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->pluck('customer_id');

                $models->whereIn('id', $customerIds);
            }

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = $models->count();
            $totalPages = ceil($totalItems / $perPage);

            $paginatedModels = $models->forPage($currentPage, $perPage)->get();

            $pagination = [
                'per_page' => (int) $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ];

            $response = [
                'message' => 'success',
                'data' => $paginatedModels,
                'pagination' => $pagination,
            ];

            return response()->json($response, 200);
        }

        // if ($request->has('date'))
        if ($request->has('group') && $request->group === 'date') {
            $response = $this->GroupByDate($request);
            return response()->json($response, 200);
        }

    }

    public function grandTotal(Request $request)
    {

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
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }
            $models = $query->get();

            $data['item_sold'] = $models->sum('item_sold');
            $data['sub_total'] = $models->sum('sub_total');
            $data['tax'] = $models->sum('tax');
            $data['discount'] = $models->sum('discount');
            $data['total'] = $models->sum('total');

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

            $data['item_sold'] = $models->sum('item_sold');
            $data['sub_total'] = $models->sum('sub_total');
            $data['tax'] = $models->sum('tax');
            $data['discount'] = $models->sum('discount');
            $data['total'] = $models->sum('total');

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
            $data['item_sold'] = $models->sum('item_sold');
            $data['sub_total'] = $models->sum('sub_total');
            $data['tax'] = $models->sum('tax');
            $data['discount'] = $models->sum('discount');
            $data['total'] = $models->sum('total');

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
                        $q->where('order_type', 'sales')
                            ->where('status', 'done')
                            ->whereBetween('date', [$request->start_date, $request->end_date]);
                    })
                    ->pluck('product_id');

                $query->whereIn('id', $productIds);
            }
            $models = $query->get();
            $data['item_sold'] = $models->sum('item_sold');
            $data['sub_total'] = $models->sum('sub_total');
            $data['tax'] = $models->sum('tax');
            $data['discount'] = $models->sum('discount');
            $data['total'] = $models->sum('total');

            return $data;

        }
        // if ($request->has('customer'))
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
                $customerIds = $this->orderModel->where('order_type', 'sales')
                    ->where('status', 'done')
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->pluck('customer_id');

                $query->whereIn('id', $customerIds);
            }
            $models = $query->get();

            $data['item_sold'] = $models->sum('item_sold');
            $data['sub_total'] = $models->sum('sub_total');
            $data['tax'] = $models->sum('tax');
            $data['discount'] = $models->sum('discount');
            $data['total'] = $models->sum('total');

            return $data;
        }
        // if ($request->has('date'))
        if ($request->has('group') && $request->group === 'date') {
            $response = $this->GroupByDate($request)->getContent();
            $responseData = json_decode($response, true);

            // Now you can access the data within the response
            $summary = [
                'item_sold' => collect($responseData['data'])->sum('item_sold'),
                'sub_total' => collect($responseData['data'])->sum('sub_total'),
                'tax' => collect($responseData['data'])->sum('tax'),
                'discount' => collect($responseData['data'])->sum('discount'),
                'total' => collect($responseData['data'])->sum('total'),
            ];

            return $summary;
        }

    }

    public function GroupByDate(Request $request)
    {
        $models = $this->orderModel->where('order_type', 'sales')
            ->where('status', 'done')->whereHas('items', function ($q) {
            $q->where('type', 'sales');
        });

        if ($request->branch_id) {
            $models->whereHas('items.product', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->start_date && $request->end_date) {
            $customerIds = $this->orderModel->where('order_type', 'sales')
                ->where('status', 'done')
                ->whereBetween('date', [$request->start_date, $request->end_date])
                ->pluck('customer_id');

            $models->whereIn('customer_id', $customerIds);
        }

        $models = $models->get()->groupBy('date');

        $data = [];
        foreach ($models as $key => $model) {
            $order_id = $model->pluck('id')->toArray();

            $orderItems = OrderItem::where('type', 'sales')->whereIn('order_id', $order_id)
                ->whereHas('order', function ($query) {
                    $query->where('order_type', 'sales')
                        ->where('status', 'done');
                })->get();

            $data[] = [
                'id' => '', // Add appropriate id if needed
                'name' => $key,
                'name_e' => $key, // You can set this as well
                'created_at' => null, // If applicable
                'updated_at' => null, // If applicable
                'company_id' => null, // If applicable
                'item_sold' => $orderItems->sum('quantity'),
                'sub_total' => $model->sum('sub_total'),
                'tax' => $model->sum('total_tax'),
                'discount' => $model->sum('all_discount'),
                'total' => $model->sum('total'),
                'item_purchased' => 0, // You can set this as well
                'total_purchased' => 0, // You can set this as well
                'products' => [], // You can set this as well
            ];
        }

        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);
        $totalItems = count($data); // Count of items in your $data array
        $totalPages = ceil($totalItems / $perPage);

        $response = [
            'message' => 'success',
            'data' => array_slice($data, ($currentPage - 1) * $perPage, $perPage),

            'pagination' => [
                'per_page' => $perPage,
                'path' => $request->fullUrl(),
                'total' => $totalItems,
                'current_page' => $currentPage,
                'next_page_url' => ($currentPage < $totalPages) ? $request->fullUrl() . '?page=' . ($currentPage + 1) : null,
                'previous_page_url' => ($currentPage > 1) ? $request->fullUrl() . '?page=' . ($currentPage - 1) : null,
                'last_page' => $totalPages,
                'has_more_pages' => ($currentPage < $totalPages),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $totalItems),
            ],
        ];

        return response()->json($response, 200);
    }

}
