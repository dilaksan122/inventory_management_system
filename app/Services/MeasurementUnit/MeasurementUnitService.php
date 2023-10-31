<?php

namespace App\Services\MeasurementUnit;

use App\Models\MeasurementUnit;
use App\Services\BaseService;

/**
 * MeasurementUnitService
 */
class MeasurementUnitService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(MeasurementUnit $model)
    {
        parent::__construct($model);
    }
}
