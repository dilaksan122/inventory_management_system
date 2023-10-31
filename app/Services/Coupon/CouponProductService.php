<?php

namespace App\Services\Coupon;

use App\Models\CouponProduct;
use App\Services\BaseService;

/**
 * BrandService
 */
class CouponProductService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(CouponProduct $model)
    {
        parent::__construct($model);
    }
}
