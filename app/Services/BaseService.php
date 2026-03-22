<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected Model $model;

    /**
     * Zwraca wszystkie rekordy z bazy.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Zwraca rekordy z paginacją (domyślnie 10 na stronę).
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        // Używamy query(), aby zacząć budować zapytanie na modelu
        return $this->model->query()->paginate($perPage);
    }
}
