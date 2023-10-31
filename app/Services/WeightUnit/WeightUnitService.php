<?php

namespace App\Services\WeightUnit;

use App\Models\WeightUnit;
use App\Services\BaseService;

/**
 * WeightUnitService
 */
class WeightUnitService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(WeightUnit $model)
    {
        parent::__construct($model);
    }
}
