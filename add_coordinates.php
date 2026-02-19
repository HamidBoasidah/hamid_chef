<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "إضافة إحداثيات للعناوين...\n";
echo "========================================\n\n";

// إحداثيات مدن سعودية مختلفة
$cities = [
    ['name' => 'الرياض', 'lat' => 24.7136, 'lng' => 46.6753],
    ['name' => 'جدة', 'lat' => 21.5433, 'lng' => 39.1728],
    ['name' => 'مكة', 'lat' => 21.3891, 'lng' => 39.8579],
    ['name' => 'المدينة', 'lat' => 24.5247, 'lng' => 39.5692],
    ['name' => 'الدمام', 'lat' => 26.4207, 'lng' => 50.0888],
    ['name' => 'الطائف', 'lat' => 21.2703, 'lng' => 40.4150],
];

$addresses = App\Models\Address::all();

foreach ($addresses as $index => $address) {
    $city = $cities[$index % count($cities)];

    // إضافة تباين عشوائي صغير للإحداثيات (±0.1 درجة تقريباً 11 كم)
    $lat = $city['lat'] + (rand(-100, 100) / 1000);
    $lng = $city['lng'] + (rand(-100, 100) / 1000);

    $address->update([
        'lat' => $lat,
        'lang' => $lng
    ]);

    echo "✓ تم تحديث العنوان #{$address->id} - {$city['name']} (lat: {$lat}, lng: {$lng})\n";
}

echo "\n========================================\n";
echo "تم تحديث {$addresses->count()} عنوان بنجاح\n";
echo "========================================\n";
