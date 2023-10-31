<?php

namespace App\Services\Manufacturer;

use App\Models\Manufacturer;
use App\Services\BaseService;

/**
 * ManufacturerService
 */
class ManufacturerService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Manufacturer $model)
    {
        parent::__construct($model);
    }
}
