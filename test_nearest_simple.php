<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "اختبار API البحث عن أقرب الشيفات\n";
echo "========================================\n\n";

$lat = 24.7136;
$lng = 46.6753;

$chefService = app(App\Services\ChefService::class);

// Test 1: بحث أساسي
echo "Test 1: بحث أساسي (نصف قطر 100 كم)\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 100, 10);
    echo "✓ نجح\n";
    echo "عدد الشيفات: {$chefs->total()}\n\n";

    if ($chefs->count() > 0) {
        echo "أول 3 شيفات:\n";
        foreach ($chefs->take(3) as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | التقييم: {$chef->rating_avg} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 2: بحث نصي
echo "Test 2: بحث نصي (search=شيف)\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 100, 10, ['search' => 'شيف']);
    echo "✓ نجح\n";
    echo "عدد النتائج: {$chefs->total()}\n";

    if ($chefs->count() > 0) {
        foreach ($chefs->take(2) as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 3: فلتر حسب التصنيف
echo "Test 3: فلتر حسب التصنيف (category_id=1)\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 100, 10, ['category_id' => 1]);
    echo "✓ نجح\n";
    echo "عدد الشيفات في التصنيف 1: {$chefs->total()}\n";

    if ($chefs->count() > 0) {
        foreach ($chefs->take(2) as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 4: فلتر حسب الوسم
echo "Test 4: فلتر حسب الوسم (tag_id=1)\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 100, 10, ['tag_id' => 1]);
    echo "✓ نجح\n";
    echo "عدد الشيفات مع الوسم 1: {$chefs->total()}\n";

    if ($chefs->count() > 0) {
        foreach ($chefs->take(2) as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 5: فلتر حسب التقييم
echo "Test 5: فلتر حسب التقييم (min_rating=4)\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 100, 10, ['min_rating' => 4]);
    echo "✓ نجح\n";
    echo "عدد الشيفات بتقييم 4+: {$chefs->total()}\n";

    if ($chefs->count() > 0) {
        foreach ($chefs->take(3) as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | التقييم: {$chef->rating_avg} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 6: فلاتر متعددة
echo "Test 6: فلاتر متعددة\n";
echo "----------------------------------------\n";
try {
    $chefs = $chefService->searchNearestChefs($lat, $lng, 50, 5, [
        'min_rating' => 3,
        'max_price' => 300
    ]);
    echo "✓ نجح\n";
    echo "عدد النتائج: {$chefs->total()}\n";
    echo "الصفحة الحالية: {$chefs->currentPage()} من {$chefs->lastPage()}\n\n";

    if ($chefs->count() > 0) {
        echo "النتائج:\n";
        foreach ($chefs as $chef) {
            $distance = round($chef->distance, 2);
            echo "  - {$chef->name} | التقييم: {$chef->rating_avg} | السعر: {$chef->base_hourly_rate} | المسافة: {$distance} كم\n";
        }
    }
} catch (Exception $e) {
    echo "✗ فشل: {$e->getMessage()}\n";
}
echo "\n";

// Test 7: اختبار نصف القطر المختلف
echo "Test 7: اختبار نصف القطر المختلف\n";
echo "----------------------------------------\n";
$radii = [10, 50, 100, 200];

foreach ($radii as $radius) {
    echo "نصف القطر: {$radius} كم\n";
    try {
        $chefs = $chefService->searchNearestChefs($lat, $lng, $radius, 10);
        echo "  ✓ عدد الشيفات: {$chefs->total()}\n";

        if ($chefs->count() > 0) {
            $closest = $chefs->first();
            $distance = round($closest->distance, 2);
            echo "    الأقرب: {$closest->name} - {$distance} كم\n";
        }
    } catch (Exception $e) {
        echo "  ✗ فشل: {$e->getMessage()}\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "انتهى الاختبار\n";
echo "========================================\n";
