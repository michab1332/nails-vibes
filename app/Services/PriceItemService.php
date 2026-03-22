<?php

namespace App\Services;

use App\Enums\PriceItemCategory;
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

    public function create(): array
    {
        return [
            'categories' => PriceItemCategory::cases(),
        ];
    }

    public function store(array $data): PriceItem
    {
        return $this->model->create($data);
    }

    public function edit(PriceItem $priceItem): array
    {
        return [
            'priceItem' => $priceItem,
            'categories' => PriceItemCategory::cases(),
        ];
    }

    public function update(PriceItem $priceItem, array $data): bool
    {
        return $priceItem->update($data);
    }

    public function destroy(PriceItem $priceItem): ?bool
    {
        return $priceItem->delete();
    }
}
