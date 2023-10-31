<?php

namespace App\Http\Controllers\Api\v100\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleReturnRequestResource;
use App\Http\Resources\SaleReturnResource;
use App\Models\Invoice;
use App\Models\SaleReturn;
use App\Services\Sale\SaleReturnRequestServices;
use App\Traits\ApiReturnFormatTrait;
use DB;

class InvoiceReturnController extends Controller
{
    use ApiReturnFormatTrait;
    protected $saleReturnRequestServices;

    public function __construct(SaleReturnRequestServices $saleReturnRequestServices)
    {
        $this->saleReturnRequestServices = $saleReturnRequestServices;
    }
    public function index(){
        $returnable_invoice = Invoice::with('warehouse')
            ->orderBy('id', 'DESC')
            ->where(function ($query){
                $query->where('status', 'paid')
                    ->orWhere('status', 'partially_paid');
            })
            ->select('invoices.*')->paginate(10);
        return $this->responseWithSuccess('Returnable Invoice List',$returnable_invoice);
    }
    public function returnRequests(){
        $sale_returns = SaleReturnRequestResource::collection($this->saleReturnRequestServices->returnRequestList());
        return $this->responseWithSuccess('Return Request List',$sale_returns);
    }

}
