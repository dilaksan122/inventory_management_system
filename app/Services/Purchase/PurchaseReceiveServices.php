<?php

namespace App\Services\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Traits\SetModel;
use App\Models\ProductStock;
use App\Services\BaseService;
use App\Models\PurchaseReceive;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PurchaseReceiveServices
 */
class PurchaseReceiveServices extends BaseService
{
    use SetModel;

    public $product;
    public $product_stock;

    /**
     * __construct
     *
     * @param  mixed $model
     * @param  mixed $product
     * @param  mixed $product_stock
     * @return void
     */
    public function __construct(PurchaseReceive $model, Product $product, ProductStock $product_stock)
    {
        $this->model = $model;
        $this->product = $product;
        $this->product_stock = $product_stock;
    }

    /**
     * validate
     *
     * @param  mixed $request
     * @return PurchaseReceiveServices
     */
    public function validate($request): PurchaseReceiveServices
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'total' => 'required|numeric',
            'product_stock_id' => 'required',
            'receive_quantity.*' => 'nullable|numeric|min:0',
            'receive_price.*' => 'required|numeric',
            'receive_sub_total.*' => 'nullable|numeric'
        ]);

        return $this;
    }

    /**
     * store
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return bool
     */
    public function store($request, $purchase): bool
    {
        DB::transaction(function () use ($request, $purchase) {

            $purchase->update(['received' => true]);

            $this->storePurchaseReceive($request, $purchase->id)
                ->storePurchaseReceiveItem($request)
                ->stockUpdate($request, $purchase);
        });

        return true;
    }

    /**
     * storePurchaseReceive
     *
     * @param  mixed $request
     * @param  mixed $purchase_id
     * @return PurchaseReceiveServices
     */
    private function storePurchaseReceive($request, $purchase_id): PurchaseReceiveServices
    {

        $this->model = $this->model
            ->newQuery()
            ->create([
                'purchase_id' => $purchase_id,
                'receive_date' => $request->date,
                'total' => $request->total,
            ]);

        $purchase = Purchase::find($purchase_id);

        if($purchase && $purchase->status == Purchase::STATUS_REQUESTED) {
            $purchase->update(['status' => Purchase::STATUS_CONFIRMED]);
        }

        return $this;
    }

    /**
     * storePurchaseReceiveItem
     *
     * @param  mixed $request
     * @return PurchaseReceiveServices
     */
    private function storePurchaseReceiveItem($request): PurchaseReceiveServices
    {
        if (isset($request->purchase_item_id) && is_array($request->purchase_item_id)) {
            foreach ($request->purchase_item_id as $key => $purchase_item_id) {
                if ($request->receive_quantity[$key] > 0) {
                    $this->model
                        ->purchaseItemReceives()
                        ->create([
                            'purchase_receive_id' => $this->model->id,
                            'purchase_item_id' => $purchase_item_id,
                            'product_id' => $request->product_id[$key],
                            'product_stock_id' => $request->product_stock_id[$key],
                            'quantity' => $request->receive_quantity[$key],
                            'price' => $request->receive_price[$key],
                            'sub_total' => $request->receive_sub_total[$key],
                        ]);
                }
            }
        }

        return $this;
    }

    /**
     * stockUpdate
     *
     * @param  mixed $request
     * @param  mixed $purchase
     * @return PurchaseReceiveServices
     */
    private function stockUpdate($request, $purchase): PurchaseReceiveServices
    {
        $w_id = request('warehouse_id') ? request('warehouse_id') : optional(Warehouse::query()->where('is_default', true)->first())->id;
        if(!$w_id){
            throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
        }
        if (isset($request->product_stock_id) && is_array($request->product_stock_id)) {
            foreach ($request->product_stock_id as $key => $product_stock_id) {
                if ($request->receive_quantity[$key] > 0) {
                    $stock = $this->getProductStock($product_stock_id, $purchase);
                    if ($stock) {
                        $stock->update([
                            'quantity' => $stock->quantity + $request->receive_quantity[$key],
                        ]);
                    } else {
                        $product = $this->product->newQuery()->with('allStock')->findOrFail($request->product_id[$key]);
                        if($product->is_variant == 0){
                            $this->product_stock->newQuery()->create([
                                'product_id'    => $request->product_id[$key],
                                'warehouse_id'  => $w_id,
                                'quantity'      => $request->receive_quantity[$key],
                                'created_by'    => auth()->id(),
                                'updated_by'    => auth()->id(),
                            ]);
                        }else {
                            foreach ($product->allStock as $p_stock){
                                $this->product_stock->newQuery()->create([
                                    'product_id'            => $request->product_id[$key],
                                    'warehouse_id'          => $w_id,
                                    'attribute_id'          => $p_stock->attribute_id,
                                    'attribute_item_id'     => $p_stock->attribute_item_id,
                                    'created_by'            => auth()->id(),
                                    'updated_by'            => auth()->id(),
                                ]);
                            }
                            $old_stock = ProductStock::find($product_stock_id);
                            $stock = $this->product_stock->newQuery()
                                ->where('warehouse_id', $w_id)
                                ->where('product_id', $request->product_id[$key])
                                ->where('attribute_id', $old_stock->attribute_id)
                                ->where('attribute_item_id', $old_stock->attribute_item_id)
                                ->first();

                            $stock->update([
                                'quantity' =>  $request->receive_quantity[$key],
                            ]);
                        }
                    }

                    $productStock = $this->product->newQuery()->where('id', $request->product_id[$key])->first();
                    $productStock->update([
                        'stock' => $productStock->stock + $request->receive_quantity[$key]
                    ]);
                }
            }
        }

        return $this;
    }

    /**
     * getProductStock
     *
     * @param  mixed $product_id
     * @param  mixed $purchase
     * @return void
     */
    private function getProductStock($product_stock_id, $purchase)
    {
        if ($purchase->warehouse_id){
            $defaultWarehouse = $purchase->warehouse_id;
        }else{
            $defaultWarehouse = Warehouse::query()->where('is_default', true)->first();
            if($defaultWarehouse) {
                $defaultWarehouse = $defaultWarehouse->id;
            }else{
                throw ValidationException::withMessages(['message' => __('Select a warehouse first')]);
            }
        }
        return $this->product_stock->newQuery()
            ->where('id', $product_stock_id)
            ->where('warehouse_id', $defaultWarehouse)
            ->first();

    }
}
