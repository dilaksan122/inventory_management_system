<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\productResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Warehouse\WarehouseService;
use App\Traits\ApiReturnFormatTrait;
use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiReturnFormatTrait;
    protected $warehouseService;
    protected $productService;

    public function __construct(WarehouseService $warehouseService,ProductService $productService){
        $this->warehouseService = $warehouseService;
        $this->productService = $productService;
    }
    public function index(){
        $products = ProductResource::collection(Product::with(['category:id,name', 'manufacturer:id,name', 'weight_unit:id,name'])->newQuery()->select('products.*')->paginate(10));
        return $this->responseWithSuccess('Product List',$products);
    }
    public function warehouseAndStockWiseProducts(Request $request){
        $warehouses = $this->warehouseService->pluck();
        $warehouse = count($warehouses) > 1 && \request('warehouse')
            ? $this->warehouseService->getWareHouse(\request('warehouse'))
            : $this->warehouseService->firstWarehouse();

       if(!$warehouse) {
           return $this->responseWithError('Warehouse not found', [], 404);
       }

        $productStocks = $this->productService->wareHouseWiseAllProductStocks(['product','attribute:id,name','attributeItem:id,name,attribute_id,color'], $warehouse);

        return $this->responseWithSuccess('Warehouse And Stock Wise Product List', $productStocks);
    }
}
