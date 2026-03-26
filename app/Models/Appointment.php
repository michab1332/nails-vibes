<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'start_time',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['scope'] ?? 'current', function ($query, $scope) use ($filters) {
            if ($scope === 'current' && !isset($filters['from_date'])) {
                $query->where('start_time', '>=', now()->startOfDay());
            }
        })->when($filters['from_date'] ?? null, function ($query, $date) {
            $query->whereDate('start_time', '>=', $date);
        })->when($filters['to_date'] ?? null, function ($query, $date) {
            $query->whereDate('start_time', '<=', $date);
        })->when($filters['clients'] ?? null, function ($query, $clients) {
            $query->whereIn('client_id', (array) $clients);
        })->when($filters['statuses'] ?? null, function ($query, $statuses) {
            $query->whereIn('status', (array) $statuses);
        })->when($filters['search'] ?? null, function ($query, $search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function priceItems(): BelongsToMany
    {
        return $this->belongsToMany(PriceItem::class, 'appointment_price_item');
    }
}
