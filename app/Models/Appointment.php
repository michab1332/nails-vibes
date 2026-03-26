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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function priceItems(): BelongsToMany
    {
        return $this->belongsToMany(PriceItem::class, 'appointment_price_item');
    }
}
