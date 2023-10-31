<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Traits\ApiReturnFormatTrait;
use DB;

class PurchaseController extends Controller
{
    use ApiReturnFormatTrait;

    public function __construct()
    {

    }
    public function index(){
        $purchases = PurchaseResource::collection(Purchase::with(['supplier:id,first_name,last_name,email,phone', 'warehouse:id,name', 'purchaseItems'])->newQuery()->select('purchases.*')->paginate(10));
        return $this->responseWithSuccess('Purchase List',$purchases);
    }

}
