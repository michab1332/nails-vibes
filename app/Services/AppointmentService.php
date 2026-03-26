<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\PriceItem;
use App\Enums\AppointmentStatus;
use Illuminate\Support\Facades\DB;

class AppointmentService extends BaseService
{
    public function __construct(Appointment $model)
    {
        $this->model = $model;
    }

    public function index(array $filters = []): array
    {
        return [
            'appointments' => $this->model->with(['client', 'priceItems'])
                ->filter($filters)
                ->orderBy('start_time', 'asc')
                ->get(),
            'clients' => Client::select('id', 'name', 'phone_number')->get(),
            'statuses' => AppointmentStatus::cases(),
            'filters' => $filters,
        ];
    }

    public function create(): array
    {
        return [
            'clients' => Client::select('id', 'name', 'phone_number')->get(),
            'priceItems' => PriceItem::select('id', 'name', 'price_min', 'price_max')->get(),
            'statuses' => array_map(fn($status) => $status->value, AppointmentStatus::cases()),
        ];
    }

    public function store(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $appointment = $this->model->create($data);

            if (isset($data['price_items'])) {
                $appointment->priceItems()->sync($data['price_items']);
            }

            return $appointment;
        });
    }

    public function edit(Appointment $appointment): array
    {
        return [
            'appointment' => $appointment->load('priceItems'),
            'clients' => Client::select('id', 'name', 'phone_number')->get(),
            'priceItems' => PriceItem::select('id', 'name', 'price_min', 'price_max')->get(),
            'statuses' => array_map(fn($status) => $status->value, AppointmentStatus::cases()),
        ];
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        return DB::transaction(function () use ($appointment, $data) {
            $appointment->update($data);

            if (isset($data['price_items'])) {
                $appointment->priceItems()->sync($data['price_items']);
            }

            return $appointment;
        });
    }

    public function destroy(Appointment $appointment): ?bool
    {
        return $appointment->delete();
    }
}
