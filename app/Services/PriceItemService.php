<?php

namespace App\Services;

use App\Services\BaseService;
use App\Models\PriceItem;

class PriceItemService extends BaseService
{
    public function __construct(PriceItem $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->all();
    }
}
