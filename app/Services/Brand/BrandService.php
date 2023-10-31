<?php

namespace App\Services\Brand;

use App\Models\Brand;
use App\Services\BaseService;

/**
 * BrandService
 */
class BrandService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }
}
