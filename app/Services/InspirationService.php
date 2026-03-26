<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InspirationService
{
    // Łączymy kilka sprawdzonych źródeł, aby ominąć limit 20 zdjęć z jednego RSS
    protected array $sources = [
        'indigonails/%2B50-spring-ideas-for-nails', // Twoje Indigo Nails (Spring)
        'indigonails/summer-nails',                // Indigo Nails (Summer/Pastel)
        'nailitmag/spring-nails',                  // Nail It! Mag (Spring)
        'sallyhansen/spring-nails',                // Sally Hansen (Spring)
        'nailsmag',                                // Główne feedy (będą filtrowane)
        'essie'
    ];

    public function getAllInspirations(): array
    {
        return $this->getAggregatedInspirations();
    }

    protected function getAggregatedInspirations(): array
    {
        // Nowy klucz cache v12
        return Cache::remember("pinterest_multi_spring_v12", 3600, function () {
            $responses = Http::pool(fn ($pool) => array_map(
                fn ($source) => $pool->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])->timeout(20)->get("https://www.pinterest.com/" . (Str::contains($source, '/') ? $source : "{$source}/feed") . ".rss"),
                $this->sources
            ));

            $allPins = [];
            foreach ($responses as $sourceIndex => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $sourceKey = $this->sources[$sourceIndex];
                    $pins = $this->parseRss($response->body());
                    
                    // Dla głównych feedów (bez ukośnika w nazwie) filtrujemy po słowach kluczowych
                    if (!Str::contains($sourceKey, '/')) {
                        $pins = array_filter($pins, function($pin) {
                            return Str::contains(strtolower($pin['title']), ['spring', 'wiosna', 'pastel', 'flower', 'floral', 'bloom', 'mani', 'nail']);
                        });
                    }
                    
                    $allPins = array_merge($allPins, $pins);
                }
            }

            // Usuwamy duplikaty po ID
            $uniquePins = [];
            foreach ($allPins as $pin) {
                $uniquePins[$pin['id']] = $pin;
            }

            $finalPool = array_values($uniquePins);
            shuffle($finalPool); // Mieszamy, aby zdjęcia z różnych źródeł się przeplatały
            return $finalPool;
        });
    }

    protected function parseRss(string $xmlContent): array
    {
        try {
            $xml = simplexml_load_string($xmlContent);
            if (!$xml) return [];

            $items = [];
            foreach ($xml->channel->item as $item) {
                $description = (string) $item->description;
                
                // USUWAMY FILTRY SŁOWNE - bierzemy wszystko co Indigo wrzuciło na tę tablicę
                if (preg_match('/<img.+src="(.+?)"/', $description, $matches)) {
                    $items[] = [
                        'id' => (string) ($item->guid ?? Str::random(10)),
                        'title' => trim((string) $item->title),
                        'link' => (string) $item->link,
                        // Używamy formatu 736x dla maksymalnej jakości w modalu
                        'image' => str_replace('236x', '736x', $matches[1]),
                        'thumbnail' => str_replace('236x', '474x', $matches[1]),
                    ];
                }
            }
            return $items;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function fetchRss(string $url): array
    {
        // Ta metoda nie jest już używana bezpośrednio w pętli, ale zostawiamy dla kompatybilności wstecznej jeśli trzeba
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->timeout(5)->get($url);

            if (!$response->successful()) return [];

            return $this->parseRss($response->body());
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCategories(): array
    {
        return array_keys($this->sources);
    }
}
