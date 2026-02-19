# ============================================
# اختبار API البحث عن أقرب الشيفات
# ============================================

$baseUrl = "http://127.0.0.1:8000/api"
$headers = @{
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "اختبار API البحث عن أقرب الشيفات" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# استخدام إحداثيات الرياض كمثال
$lat = 24.7136
$lng = 46.6753

Write-Host "استخدام الإحداثيات: Lat=$lat, Lng=$lng" -ForegroundColor Green
Write-Host ""

# ============================================
# Test 1: بحث أساسي عن أقرب الشيفات
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 1: بحث أساسي عن أقرب الشيفات" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد الشيفات: $($response.data.data.Count)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        Write-Host "`nأول 3 شيفات:" -ForegroundColor Yellow
        $response.data.data | Select-Object -First 3 | ForEach-Object {
            Write-Host "  - $($_.name) | التقييم: $($_.rating_avg) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 2: بحث نصي شامل
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 2: بحث نصي شامل (search)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$searchTerms = @("شيف", "طعام", "مشويات")

foreach ($term in $searchTerms) {
    Write-Host "`nبحث عن: '$term'" -ForegroundColor Yellow

    try {
        $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100&search=$term"
        $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

        Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
        Write-Host "عدد النتائج: $($response.data.data.Count)" -ForegroundColor White

        if ($response.data.data.Count -gt 0) {
            $response.data.data | Select-Object -First 2 | ForEach-Object {
                Write-Host "  - $($_.name) | المسافة: $($_.distance_text)" -ForegroundColor White
            }
        }
    } catch {
        Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""

# ============================================
# Test 3: فلتر حسب التصنيف
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 3: فلتر حسب التصنيف (category_id)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100&category_id=1"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد الشيفات في التصنيف 1: $($response.data.data.Count)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        $response.data.data | Select-Object -First 2 | ForEach-Object {
            Write-Host "  - $($_.name) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 4: فلتر حسب الوسم
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 4: فلتر حسب الوسم (tag_id)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100&tag_id=1"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد الشيفات مع الوسم 1: $($response.data.data.Count)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        $response.data.data | Select-Object -First 2 | ForEach-Object {
            Write-Host "  - $($_.name) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 5: فلتر حسب التقييم
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 5: فلتر حسب التقييم (min_rating)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100&min_rating=4"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد الشيفات بتقييم 4+: $($response.data.data.Count)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        $response.data.data | Select-Object -First 3 | ForEach-Object {
            Write-Host "  - $($_.name) | التقييم: $($_.rating_avg) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 6: فلتر حسب السعر
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 6: فلتر حسب السعر (max_price)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=100&max_price=200"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد الشيفات بسعر أقل من 200: $($response.data.data.Count)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        $response.data.data | Select-Object -First 3 | ForEach-Object {
            Write-Host "  - $($_.name) | السعر: $($_.base_hourly_rate) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 7: فلاتر متعددة
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 7: فلاتر متعددة" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=50&min_rating=3&max_price=300&per_page=5"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

    Write-Host "✓ النتيجة: نجح" -ForegroundColor Green
    Write-Host "عدد النتائج: $($response.data.data.Count)" -ForegroundColor White
    Write-Host "الصفحة الحالية: $($response.data.current_page) من $($response.data.last_page)" -ForegroundColor White

    if ($response.data.data.Count -gt 0) {
        Write-Host "`nالنتائج:" -ForegroundColor Yellow
        $response.data.data | ForEach-Object {
            Write-Host "  - $($_.name) | التقييم: $($_.rating_avg) | السعر: $($_.base_hourly_rate) | المسافة: $($_.distance_text)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# ============================================
# Test 8: اختبار Validation
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 8: اختبار Validation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

Write-Host "`n8.1 بدون lat (يجب أن يفشل)" -ForegroundColor Yellow
try {
    $url = "$baseUrl/chefs/nearest?lng=$lng"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers
    Write-Host "✗ لم يفشل كما متوقع" -ForegroundColor Red
} catch {
    Write-Host "✓ فشل كما متوقع: lat مطلوب" -ForegroundColor Green
}

Write-Host "`n8.2 lat خارج النطاق (يجب أن يفشل)" -ForegroundColor Yellow
try {
    $url = "$baseUrl/chefs/nearest?lat=100&lng=$lng"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers
    Write-Host "✗ لم يفشل كما متوقع" -ForegroundColor Red
} catch {
    Write-Host "✓ فشل كما متوقع: lat يجب أن يكون بين -90 و 90" -ForegroundColor Green
}

Write-Host "`n8.3 category_id غير موجود (يجب أن يفشل)" -ForegroundColor Yellow
try {
    $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&category_id=9999"
    $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers
    Write-Host "✗ لم يفشل كما متوقع" -ForegroundColor Red
} catch {
    Write-Host "✓ فشل كما متوقع: category_id غير موجود" -ForegroundColor Green
}

Write-Host ""

# ============================================
# Test 9: اختبار نصف القطر
# ============================================
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test 9: اختبار نصف القطر المختلف" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$radii = @(10, 50, 100, 200)

foreach ($radius in $radii) {
    Write-Host "`nنصف القطر: $radius كم" -ForegroundColor Yellow

    try {
        $url = "$baseUrl/chefs/nearest?lat=$lat&lng=$lng&radius=$radius"
        $response = Invoke-RestMethod -Uri $url -Method Get -Headers $headers

        Write-Host "✓ عدد الشيفات: $($response.data.data.Count)" -ForegroundColor Green

        if ($response.data.data.Count -gt 0) {
            $closest = $response.data.data | Select-Object -First 1
            Write-Host "  الأقرب: $($closest.name) - $($closest.distance_text)" -ForegroundColor White
        }
    } catch {
        Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "انتهى الاختبار" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
