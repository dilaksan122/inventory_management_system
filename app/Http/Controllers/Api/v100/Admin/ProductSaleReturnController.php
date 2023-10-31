<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleReturnRequestResource;
use App\Http\Resources\SaleReturnResource;
use App\Models\SaleReturn;
use App\Services\Sale\SaleReturnRequestServices;
use App\Traits\ApiReturnFormatTrait;
use DB;

class ProductSaleReturnController extends Controller
{
    use ApiReturnFormatTrait;
    protected $saleReturnRequestServices;

    public function __construct(SaleReturnRequestServices $saleReturnRequestServices)
    {
        $this->saleReturnRequestServices = $saleReturnRequestServices;
    }
    public function index(){
        $sale_returns = SaleReturnResource::collection(SaleReturn::with(['invoice', 'invoice.customerInfo', 'saleReturnItems'])->newQuery()->select('sale_returns.*')->paginate(10));
        return $this->responseWithSuccess('Sale Return List',$sale_returns);
    }
    public function returnRequests(){
        $sale_returns = SaleReturnRequestResource::collection($this->saleReturnRequestServices->returnRequestList());
        return $this->responseWithSuccess('Sale Return Request List',$sale_returns);
    }

}
