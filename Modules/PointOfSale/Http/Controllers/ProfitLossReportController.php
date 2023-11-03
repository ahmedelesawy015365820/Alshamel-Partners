<?php

namespace Modules\PointOfSale\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;

class ProfitLossReportController extends Controller
{
    public function __construct(private Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }
    public function all(Request $request)
    {

        if ($request->has('group') && $request->group === 'year') {

            $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $models = $query->get()->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('Y');
            });

            $data = [];

            foreach ($models as $year => $model) {
                $data[] = [
                    'year' => $year,
                    'Grand_total' => $model->sum('total'),
                    'tax' => $model->sum('total_tax'),
                    'profit_amount' => $model->sum('profit'),
                ];
            }

            $perPage = $request->query('per_page', 10); // Default to 10 if per_page is not specified
            $currentPage = $request->query('page', 1);
            $totalItems = count($data);
            $totalPages = ceil($totalItems / $perPage);

            $paginatedData = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

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

            return [
                'message' => 'success',
                'data' => $paginatedData,
                'pagination' => $pagination,
            ];
        }

        if ($request->has('group') && $request->group === 'month') {

            $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $models = $query->get()->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('m');
            });

            $data = [];

            foreach ($models as $month => $model) {
                $fullMonthName = Carbon::createFromFormat('m', $month)->format('F');

                $data[] = [
                    'month' => $fullMonthName,
                    'Grand_total' => $model->sum('total'),
                    'tax' => $model->sum('total_tax'),
                    'profit_amount' => $model->sum('profit'),
                ];
            }

            $perPage = $request->query('per_page', 10); // Default to 10 if per_page is not specified
            $currentPage = $request->query('page', 1);
            $totalItems = count($data);
            $totalPages = ceil($totalItems / $perPage);

            $paginatedData = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

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

            return [
                'message' => 'success',
                'data' => $paginatedData,
                'pagination' => $pagination,
            ];
        }

        if ($request->has('group') && $request->group === 'date') {

            $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $models = $query->get()->groupBy('date');

            $data = [];

            foreach ($models as $date => $model) {
                $data[] = [
                    'date' => $date,
                    'Grand_total' => $model->sum('total'),
                    'tax' => $model->sum('total_tax'),
                    'profit_amount' => $model->sum('profit'),
                ];
            }

            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $totalItems = count($data);
            $totalPages = ceil($totalItems / $perPage);

            $paginatedData = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

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

            return [
                'message' => 'success',
                'data' => $paginatedData,
                'pagination' => $pagination,
            ];
        }

        if ($request->has('group') && $request->group === 'customer') {

            $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $models = $query->get()->groupBy('customer_id');

            $data = [];

            foreach ($models as $model) {
                foreach ($model as $customer) {
                    $data[] = [
                        'customer' => $customer->customer->name ?? '',
                        'Grand_total' => $model->sum('total'),
                        'tax' => $model->sum('total_tax'),
                        'profit_amount' => $model->sum('profit'),
                    ];
                }
            }

            $perPage = $request->query('per_page', 10); // Default to 10 if per_page is not specified
            $currentPage = $request->query('page', 1);
            $totalItems = count($data);
            $totalPages = ceil($totalItems / $perPage);

            $paginatedData = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

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

            return [
                'message' => 'success',
                'data' => $paginatedData,
                'pagination' => $pagination,
            ];
        }

        if ($request->has('group') && $request->group === 'invoice_id') {

            $query = $this->orderModel->where('order_type', 'sales')->where('status', 'done');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $models = $query->get()->groupBy('prefix');

            $data = [];

            foreach ($models as $prefix => $model) {
                $data[] = [
                    'invoice_id' => $prefix,
                    'Grand_total' => $model->sum('total'),
                    'tax' => $model->sum('total_tax'),
                    'profit_amount' => $model->sum('profit'),
                ];
            }

            $perPage = $request->query('per_page', 10); // Default to 10 if per_page is not specified
            $currentPage = $request->query('page', 1);
            $totalItems = count($data);
            $totalPages = ceil($totalItems / $perPage);

            $paginatedData = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

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

            return [
                'message' => 'success',
                'data' => $paginatedData,
                'pagination' => $pagination,
            ];
        }

    }

    public function grandTotal(Request $request)
    {
        $allApiResponse = $this->all($request);

        $allData = $allApiResponse['data'];

        $sums = [
            'Grand_total' => collect($allData)->sum('Grand_total'),
            'tax' => collect($allData)->sum('tax'),
            'profit_amount' => collect($allData)->sum('profit_amount'),
        ];

        return ['message' => 'success', 'data' => [$sums]];

    }
}
