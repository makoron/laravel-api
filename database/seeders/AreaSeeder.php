<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'hokkaido' => [
                ['name' => '札幌・小樽', 'slug' => 'sapporo-otaru'],
                ['name' => '函館・道南', 'slug' => 'hakodate-donan'],
                ['name' => '富良野・美瑛・旭川', 'slug' => 'furano-biei-asahikawa'],
                ['name' => '道東', 'slug' => 'doto'],
            ],

            'nagano' => [
                ['name' => '長野・戸隠', 'slug' => 'nagano-togakushi'],
                ['name' => '松本・安曇野', 'slug' => 'matsumoto-azumino'],
                ['name' => '軽井沢・佐久', 'slug' => 'karuizawa-saku'],
                ['name' => '上高地・白馬', 'slug' => 'kamikochi-hakuba'],
            ],

            'shizuoka' => [
                ['name' => '西部', 'slug' => 'shizuoka-west'],
                ['name' => '中部', 'slug' => 'shizuoka-central'],
                ['name' => '東部', 'slug' => 'shizuoka-east'],
                ['name' => '伊豆', 'slug' => 'izu'],
            ],

            'kyoto' => [
                ['name' => '京都市内', 'slug' => 'kyoto-city'],
                ['name' => '宇治・南山城', 'slug' => 'uji-minamiyamashiro'],
                ['name' => '亀岡・丹波', 'slug' => 'kameoka-tanba'],
                ['name' => '丹後・天橋立', 'slug' => 'tango-amanohashidate'],
            ],
        ];

        foreach ($data as $prefectureSlug => $areas) {
            $prefecture = Prefecture::where('slug', $prefectureSlug)->first();

            if (! $prefecture) {
                continue;
            }

            foreach ($areas as $index => $areaData) {
                Area::updateOrCreate(
                    ['slug' => $areaData['slug']],
                    [
                        'prefecture_id' => $prefecture->id,
                        'name' => $areaData['name'],
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
