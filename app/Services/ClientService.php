<?php

namespace App\Services;

use App\Models\Client;
use App\Services\BaseService;

class ClientService extends BaseService
{
    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->latest()->paginate(10);
    }

    public function store(array $data): Client
    {
        return $this->model->create($data);
    }

    public function update(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    public function destroy(Client $client): ?bool
    {
        return $client->delete();
    }
}
