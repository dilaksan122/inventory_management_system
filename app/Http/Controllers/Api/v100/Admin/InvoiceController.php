<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Services\Invoice\InvoiceService;
use App\Traits\ApiReturnFormatTrait;
use DB;

class InvoiceController extends Controller
{
    use ApiReturnFormatTrait;
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    public function index(){
        $purchases = InvoiceResource::collection($this->invoiceService->getInvoiceList());
        return $this->responseWithSuccess('Invoice List',$purchases);
    }
    public function show($id){
        $invoice = $this->invoiceService->get($id, ['items', 'payments','customerInfo','saleReturns','warehouse:id,name']);
        return $this->responseWithSuccess('Invoice Details',$invoice);
    }
    public function download($id){
        return $this->invoiceService->download($id);
    }

}
