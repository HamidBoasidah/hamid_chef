<?php

/**
 * اختبار API البحث عن أقرب الشيفات
 */

$baseUrl = 'http://127.0.0.1:8000/api';
$lat = 24.7136;
$lng = 46.6753;

echo "========================================\n";
echo "اختبار API البحث عن أقرب الشيفات\n";
echo "========================================\n\n";

function makeRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Test 1: بحث أساسي
echo "Test 1: بحث أساسي عن أقرب الشيفات\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100";
$result = makeRequest($url);

if ($result['code'] === 200) {
    echo "✓ نجح (HTTP {$result['code']})\n";
    $chefs = $result['data']['data']['data'] ?? [];
    echo "عدد الشيفات: " . count($chefs) . "\n\n";

    if (count($chefs) > 0) {
        echo "أول 3 شيفات:\n";
        foreach (array_slice($chefs, 0, 3) as $chef) {
            echo "  - {$chef['name']} | التقييم: {$chef['rating_avg']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
    echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n";

// Test 2: بحث نصي
echo "Test 2: بحث نصي شامل\n";
echo "----------------------------------------\n";
$searchTerms = ['شيف', 'طعام', 'مشويات'];

foreach ($searchTerms as $term) {
    echo "بحث عن: '$term'\n";
    $url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100&search=" . urlencode($term);
    $result = makeRequest($url);

    if ($result['code'] === 200) {
        $chefs = $result['data']['data']['data'] ?? [];
        echo "  ✓ عدد النتائج: " . count($chefs) . "\n";

        if (count($chefs) > 0) {
            foreach (array_slice($chefs, 0, 2) as $chef) {
                echo "    - {$chef['name']} | المسافة: {$chef['distance_text']}\n";
            }
        }
    } else {
        echo "  ✗ فشل (HTTP {$result['code']})\n";
    }
    echo "\n";
}

// Test 3: فلتر حسب التصنيف
echo "Test 3: فلتر حسب التصنيف (category_id=1)\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100&category_id=1";
$result = makeRequest($url);

if ($result['code'] === 200) {
    $chefs = $result['data']['data']['data'] ?? [];
    echo "✓ عدد الشيفات في التصنيف 1: " . count($chefs) . "\n";

    if (count($chefs) > 0) {
        foreach (array_slice($chefs, 0, 2) as $chef) {
            echo "  - {$chef['name']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
}
echo "\n";

// Test 4: فلتر حسب الوسم
echo "Test 4: فلتر حسب الوسم (tag_id=1)\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100&tag_id=1";
$result = makeRequest($url);

if ($result['code'] === 200) {
    $chefs = $result['data']['data']['data'] ?? [];
    echo "✓ عدد الشيفات مع الوسم 1: " . count($chefs) . "\n";

    if (count($chefs) > 0) {
        foreach (array_slice($chefs, 0, 2) as $chef) {
            echo "  - {$chef['name']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
}
echo "\n";

// Test 5: فلتر حسب التقييم
echo "Test 5: فلتر حسب التقييم (min_rating=4)\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100&min_rating=4";
$result = makeRequest($url);

if ($result['code'] === 200) {
    $chefs = $result['data']['data']['data'] ?? [];
    echo "✓ عدد الشيفات بتقييم 4+: " . count($chefs) . "\n";

    if (count($chefs) > 0) {
        foreach (array_slice($chefs, 0, 3) as $chef) {
            echo "  - {$chef['name']} | التقييم: {$chef['rating_avg']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
}
echo "\n";

// Test 6: فلتر حسب السعر
echo "Test 6: فلتر حسب السعر (max_price=200)\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=100&max_price=200";
$result = makeRequest($url);

if ($result['code'] === 200) {
    $chefs = $result['data']['data']['data'] ?? [];
    echo "✓ عدد الشيفات بسعر أقل من 200: " . count($chefs) . "\n";

    if (count($chefs) > 0) {
        foreach (array_slice($chefs, 0, 3) as $chef) {
            echo "  - {$chef['name']} | السعر: {$chef['base_hourly_rate']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
}
echo "\n";

// Test 7: فلاتر متعددة
echo "Test 7: فلاتر متعددة\n";
echo "----------------------------------------\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius=50&min_rating=3&max_price=300&per_page=5";
$result = makeRequest($url);

if ($result['code'] === 200) {
    $chefs = $result['data']['data']['data'] ?? [];
    $pagination = $result['data']['data'];
    echo "✓ عدد النتائج: " . count($chefs) . "\n";
    echo "الصفحة الحالية: {$pagination['current_page']} من {$pagination['last_page']}\n\n";

    if (count($chefs) > 0) {
        echo "النتائج:\n";
        foreach ($chefs as $chef) {
            echo "  - {$chef['name']} | التقييم: {$chef['rating_avg']} | السعر: {$chef['base_hourly_rate']} | المسافة: {$chef['distance_text']}\n";
        }
    }
} else {
    echo "✗ فشل (HTTP {$result['code']})\n";
}
echo "\n";

// Test 8: اختبار Validation
echo "Test 8: اختبار Validation\n";
echo "----------------------------------------\n";

echo "8.1 بدون lat (يجب أن يفشل)\n";
$url = "{$baseUrl}/chefs/nearest?lng={$lng}";
$result = makeRequest($url);
if ($result['code'] !== 200) {
    echo "  ✓ فشل كما متوقع (HTTP {$result['code']})\n";
} else {
    echo "  ✗ لم يفشل كما متوقع\n";
}

echo "8.2 lat خارج النطاق (يجب أن يفشل)\n";
$url = "{$baseUrl}/chefs/nearest?lat=100&lng={$lng}";
$result = makeRequest($url);
if ($result['code'] !== 200) {
    echo "  ✓ فشل كما متوقع (HTTP {$result['code']})\n";
} else {
    echo "  ✗ لم يفشل كما متوقع\n";
}

echo "8.3 category_id غير موجود (يجب أن يفشل)\n";
$url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&category_id=9999";
$result = makeRequest($url);
if ($result['code'] !== 200) {
    echo "  ✓ فشل كما متوقع (HTTP {$result['code']})\n";
} else {
    echo "  ✗ لم يفشل كما متوقع\n";
}
echo "\n";

// Test 9: اختبار نصف القطر المختلف
echo "Test 9: اختبار نصف القطر المختلف\n";
echo "----------------------------------------\n";
$radii = [10, 50, 100, 200];

foreach ($radii as $radius) {
    echo "نصف القطر: {$radius} كم\n";
    $url = "{$baseUrl}/chefs/nearest?lat={$lat}&lng={$lng}&radius={$radius}";
    $result = makeRequest($url);

    if ($result['code'] === 200) {
        $chefs = $result['data']['data']['data'] ?? [];
        echo "  ✓ عدد الشيفات: " . count($chefs) . "\n";

        if (count($chefs) > 0) {
            $closest = $chefs[0];
            echo "    الأقرب: {$closest['name']} - {$closest['distance_text']}\n";
        }
    } else {
        echo "  ✗ فشل (HTTP {$result['code']})\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "انتهى الاختبار\n";
echo "========================================\n";
