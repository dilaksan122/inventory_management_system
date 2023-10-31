<?php

namespace App\Services\Sale;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\SaleReturn;
use App\Models\SaleReturnItemRequest;
use App\Models\SaleReturnItems;
use App\Models\SaleReturnRequest;
use App\Models\Warehouse;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleReturnRequestServices extends BaseService
{
    public $invoice, $saleReturnItemRequest,$product,$productStock;

    public function __construct(
        Invoice $invoice,
        SaleReturnRequest $saleReturnRequest,
        SaleReturnItemRequest $saleReturnItemRequest,
        Product $product,
        ProductStock $productStock
    ) {
        $this->invoice                  = $invoice;
        $this->model                    = $saleReturnRequest;
        $this->saleReturnItemRequest    = $saleReturnItemRequest;
        $this->product                  = $product;
        $this->productStock             = $productStock;
    }

    public function getReturnableSale($invoice_id)
    {
        return $this->invoice
            ->newQuery()
            ->with('items', 'items.salesReturnItems')
            ->findOrFail($invoice_id);
    }

    public function validate($request): SaleReturnRequestServices
    {
        $request->validate([
            'invoice_id'    => 'required|numeric',
            'return_date'   => 'required|date_format:Y-m-d',
            'return_note'   => 'nullable|string',
            'total'         => 'required|numeric'
        ]);

        return $this;
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {

            $this->storeSaleReturnRequest($request)
                ->storeSaleReturnRequestItems($request);
        });
    }

    private function storeSaleReturnRequest($request)
    {
        $this->model = $this->model
            ->newQuery()
            ->create([
                'invoice_id'            => $request->invoice_id,
                'warehouse_id'          => @$request->warehouse_id,
                'return_date'           => $request->return_date,
                'return_note'           => $request->return_note,
                'return_total_amount'   => $request->total,
                'items_info'            => $this->buildItemsObject($request),
                'requested_by'          => auth()->guard('customer')->id(),
                'created_by'            => auth()->id(),
                'updated_by'            => auth()->id(),
            ]);

        return $this;
    }

    private function buildItemsObject($request): JsonResponse
    {
        $items = [];

        foreach ($request->return_qty as $key => $return_qty) {
            if ($return_qty) {
                $items[] = [
                    'invoice_items_id'  => $request->invoice_details_id[$key],
                    'product_id'        => $request->product_id[$key],
                    'product_stock_id'  => $request->product_stock_id[$key],
                    'product_name'      => $request->product_name[$key],
                    'product_sku'       => $request->product_sku[$key],
                    'price'             => $request->price[$key],
                    'discount'          => $request->discount[$key],
                    'discount_type'     => $request->discount_type[$key],
                    'return_qty'        => $return_qty,
                    'return_price'      => $request->return_price[$key],
                    'return_sub_total'  => $request->return_sub_total[$key],
                ];
            }
        }

        return response()->json($items);
    }

    private function storeSaleReturnRequestItems($request)
    {
        $items_for_table = [];
        foreach ($request->return_qty as $key => $return_qty) {
            if ($return_qty) {
                $items_for_table[] = [
                    'sale_return_request_id'    => $this->model->id,
                    'invoice_item_id'           => $request->invoice_details_id[$key],
                    'product_id'                => $request->product_id[$key],
                    'product_stock_id'          => $request->product_stock_id[$key],
                    'product_name'              => $request->product_name[$key],
                    'return_qty'                => $return_qty,
                    'return_price'              => $request->return_price[$key],
                    'return_sub_total'          => $request->return_sub_total[$key],
                    'created_by'                => auth()->id(),
                    'updated_by'                => auth()->id(),
                ];
            }
        }

        $this->saleReturnItemRequest->newQuery()->insert($items_for_table);

        return $this;
    }

    public function returnRequestAccept($id){
        try{
            DB::beginTransaction();
            $return_request         = $this->model->newQuery()->with('saleReturnRequestItems')->findOrFail($id);
            $this->storeSaleReturn($return_request);
            $this->stockUpdate($return_request);

            $return_request->update([
                'status'                => SaleReturnRequest::STATUS_ACCEPTED,
                'status_updated_by'     => auth()->id(),
                'status_updated_at'     => now()
            ]);

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }
    public function returnRequestReject($id){
        $this->model = $this->model->newQuery()->findOrFail($id);
        $this->model->update([
            'status'                => SaleReturnRequest::STATUS_REJECTED,
            'status_updated_by'     => auth()->id(),
            'status_updated_at'     => now()
        ]);
        return $this;
    }

    private function storeSaleReturn($request)
    {
        $sale_return = SaleReturn::
            create([
                'invoice_id'            => $request->invoice_id,
                'return_date'           => now(),
                'return_note'           => $request->return_note,
                'return_total_amount'   => $request->return_total_amount,
                'items_info'            => $request->items_info,
                'created_by'            => auth()->id(),
                'updated_by'            => auth()->id(),
            ]);
        $items_for_table = [];

        foreach ($request->saleReturnRequestItems as $key => $return_request_item) {
            $items_for_table[] = [
                'sale_return_id'        => $sale_return->id,
                'invoice_item_id'       => $return_request_item->invoice_item_id,
                'product_id'            => $return_request_item->product_id,
                'product_stock_id'      => $return_request_item->product_stock_id,
                'product_name'          => $return_request_item->product_name,
                'return_qty'            => $return_request_item->return_qty,
                'return_price'          => $return_request_item->return_price,
                'return_sub_total'      => $return_request_item->return_sub_total,
                'created_by'            => auth()->id(),
                'updated_by'            => auth()->id(),
            ];
        }

        return SaleReturnItems::insert($items_for_table);
    }

    private function stockUpdate($request)
    {
        $w_id = request('warehouse_id') ? request('warehouse_id') : optional(Warehouse::query()->where('is_default', true)->first())->id;
        if(!$w_id){
            throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
        }
        foreach ($request->saleReturnRequestItems as $key => $return_request_item) {
            $product_id = $return_request_item->product_id;
            if ($return_request_item->return_qty) {
                $stock = $this->getStock($return_request_item->product_stock_id, $w_id);
                    if ($stock && $stock->warehouse_id == $w_id) {
                        $stock->update([
                            'quantity' => $stock->quantity + $return_request_item->return_qty
                        ]);
                    }
                    else{
                        $product = $this->product->newQuery()->with('allStock')->findOrFail($product_id);

                        if($product->is_variant == 0){
                            $this->productStock->newQuery()->create([
                                'product_id'    => $product_id,
                                'warehouse_id'  => $w_id,
                                'quantity'      => $return_request_item->return_qty,
//                                'attribute_id'      => $request->return_qty[$key],
//                                'attribute_item_id'      => $request->return_qty[$key],
                                'created_by'    => auth()->id(),
                                'updated_by'    => auth()->id(),
                            ]);
                        }else {

                            foreach ($product->allStock as $p_stock){
                                $this->productStock->newQuery()->create([
                                    'product_id'            => $product_id,
                                    'warehouse_id'          => $w_id,
                                    'attribute_id'          => $p_stock->attribute_id,
                                    'attribute_item_id'     => $p_stock->attribute_item_id,
                                    'created_by'            => auth()->id(),
                                    'updated_by'            => auth()->id(),
                                ]);
                            }
                            $old_stock = ProductStock::find($return_request_item->product_stock_id);
                            $stock = $this->productStock->newQuery()
                                ->where('warehouse_id', $w_id)
                                ->where('product_id', $product_id)
                                ->where('attribute_id', $old_stock->attribute_id)
                                ->where('attribute_item_id', $old_stock->attribute_item_id)
                                ->first();

                            $stock->update([
                                'quantity' => $return_request_item->return_qty
                            ]);
                        }
                    }

                    $productStock = $this->product->newQuery()->where('id', $return_request_item->product_id)->first();
                    $productStock->update([
                        'stock' => $productStock->stock + $return_request_item->return_qty
                    ]);
                }
            }
    }
    public function getStock($product_stock_id ,$warehouse_id = null)
    {
        if ($warehouse_id){
            $defaultWarehouse = $warehouse_id;
        }elseif(request('warehouse_id')){
            $defaultWarehouse = request('warehouse_id');
        }else{
            $defaultWarehouse = Warehouse::query()->where('is_default', true)->first();
            if($defaultWarehouse) {
                $defaultWarehouse = $defaultWarehouse->id;
            }else{
                throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
            }
        }
        return $this->productStock->newQuery()
            ->where('id', $product_stock_id)
            ->where('warehouse_id', $defaultWarehouse)
            ->first();
    }
    public function getPendingReturnRequestCount()
    {
        return $this->model->newQuery()
            ->where('status', SaleReturnRequest::STATUS_PENDING)
            ->count();
    }
    public function returnRequestList(){
        return SaleReturnRequest::with(['invoice.customerInfo', 'saleReturnRequestItems','requestedBy','warehouse'])
            ->orderBy('id', 'DESC')
            ->when(auth()->guard('customer')->check(), function ($query) {
                $query->whereHas('requestedBy', function ($q) {
                    $q->where('requested_by', auth()->guard('customer')->id());
                });
            })
            ->when(auth()->guard('api_customer')->check(), function ($query) {
                $query->whereHas('requestedBy', function ($q) {
                    $q->where('requested_by', auth()->guard('api_customer')->id());
                });
            })
            ->newQuery()->select('sale_return_requests.*')->paginate(10);
    }
}
