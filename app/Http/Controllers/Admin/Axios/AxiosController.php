<?php

namespace App\Http\Controllers\Admin\Axios;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\PurchaseItem;

class AxiosController extends Controller
{
    /**
     * productSearchNameSku
     *
     * @param  mixed $query
     * @return void
     */
    public function productSearchNameSku($query)
    {
        return Product::query()
            ->where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->get(['id', 'name', 'sku', 'price', 'is_variant']);
    }
    public function productStockSearchNameSku($query)
    {
        return ProductStock::query()
            ->with('product','attribute','attributeItem')
            ->whereHas('product', function ($q) use ($query) {
                $q->where('status', 'active')
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->get();
    }

    /**
     * purchaseItemDelete
     *
     * @param  mixed $query
     * @return void
     */
    public function purchaseItemDelete($query)
    {
        PurchaseItem::query()->findOrFail($query)->delete();

        return true;
    }
}
