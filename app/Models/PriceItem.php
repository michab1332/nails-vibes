<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PriceItemCategory;

class PriceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'name',
        'price_min',
        'price_max',
    ];

    protected $casts = [
        'category' => PriceItemCategory::class,
    ];

    protected $appends = [
        'formatted_price',
    ];

    public function getFormattedPriceAttribute(): string
    {
        $min = $this->price_min + 0;

        if ($this->price_max) {
            $max = $this->price_max + 0;
            return "{$min} - {$max} zł";
        }

        return "{$min} zł";
    }
}
