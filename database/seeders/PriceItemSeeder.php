<?php

namespace Database\Seeders;

use App\Models\PriceItem;
use App\Enums\PriceItemCategory;
use Illuminate\Database\Seeder;

class PriceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // PAZNOKCIE
            ['category' => PriceItemCategory::NAILS, 'name' => 'Manicure hybrydowy', 'price_min' => 130],
            ['category' => PriceItemCategory::NAILS, 'name' => 'Manicure klasyczny+odżywka', 'price_min' => 70],
            ['category' => PriceItemCategory::NAILS, 'name' => 'Zdjęcie stylizacji+manicure', 'price_min' => 80],
            ['category' => PriceItemCategory::NAILS, 'name' => 'Naprawa paznokcia po upływie terminu reklamacji (5 dni od wykonania zabiegu)', 'price_min' => 15, 'price_max' => 20],

            // PRZEDŁUŻANIE
            ['category' => PriceItemCategory::EXTENSIONS, 'name' => 'Długość ½ na szablonie', 'price_min' => 180],
            ['category' => PriceItemCategory::EXTENSIONS, 'name' => 'Długość ¾ na szablonie', 'price_min' => 190],
            ['category' => PriceItemCategory::EXTENSIONS, 'name' => 'Długość 5/6 na szablonie', 'price_min' => 200],

            // KOREKTA PAZNOKCI
            ['category' => PriceItemCategory::CORRECTION, 'name' => 'Długość ½ na szablonie', 'price_min' => 150],
            ['category' => PriceItemCategory::CORRECTION, 'name' => 'Długość ¾ na szablonie', 'price_min' => 160],
            ['category' => PriceItemCategory::CORRECTION, 'name' => 'Długość 5/6 na szablonie', 'price_min' => 170],

            // UZUPEŁNIENIE
            ['category' => PriceItemCategory::REFILL, 'name' => 'Długość na szablonie 1/2', 'price_min' => 140],
            ['category' => PriceItemCategory::REFILL, 'name' => 'Długość na szablonie 3/4', 'price_min' => 150],
            ['category' => PriceItemCategory::REFILL, 'name' => 'Długość na szablonie 5/6', 'price_min' => 160],
            ['category' => PriceItemCategory::REFILL, 'name' => 'Naprawa paznokcia po upływie terminu reklamacji (5 dni od wykonania zabiegu)', 'price_min' => 15, 'price_max' => 20],

            // ZDOBIENIA
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'French (10 paznokci)', 'price_min' => 15],
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'Babyboomer (10 paznokci)', 'price_min' => 15],
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'Ombre (1 kolor, 10 paznokci)', 'price_min' => 15],
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'Ombre (2 kolory, 10 paznokci)', 'price_min' => 20],
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'Pyłki (10 paznokci)', 'price_min' => 5],
            ['category' => PriceItemCategory::DECORATIONS, 'name' => 'Zdobienia ręcznie malowane wyceniane indywidualnie (1 paznokieć)', 'price_min' => 5, 'price_max' => 25],
        ];

        foreach ($items as $item) {
            PriceItem::updateOrCreate(
                [
                    'category' => $item['category'],
                    'name'     => $item['name'],
                ],
                [
                    'price_min' => $item['price_min'],
                    'price_max' => $item['price_max'] ?? null,
                ]
            );
        }
    }
}
