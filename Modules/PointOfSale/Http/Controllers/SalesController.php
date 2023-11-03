<?php

namespace Modules\PointOfSale\Http\Controllers;

use App\Models\Branch;
use App\Models\Tax;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Entities\Product;
use Modules\PointOfSale\Entities\ProductVariant;
use Modules\PointOfSale\Transformers\ProductResource;
use Modules\PointOfSale\Transformers\ProductSaleResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SalesController extends Controller
{
    public function __construct(private Product $productModel, private Branch $branchModel,
        private Order $orderModel,
        private OrderItem $orderItemModel,
        private Payment $paymentModel,
        private ProductVariant $productVariantModel,
        private Tax $taxModel
    ) {

        $this->productModel = $productModel;
        $this->branchModel = $branchModel;
        $this->orderModel = $orderModel;
        $this->orderItemModel = $orderItemModel;
        $this->paymentModel = $paymentModel;
        $this->productVariantModel = $productVariantModel;
        $this->taxModel = $taxModel;

    }

    public function salesProduct(Request $request)
    {
        $models = $this->productModel->attribute($request)->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ProductSaleResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function createOrders(Request $request)

    {
       return 123;

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('pointofsale::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('pointofsale::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
