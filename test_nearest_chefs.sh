#!/bin/bash

# ============================================
# اختبار API البحث عن أقرب الشيفات
# ============================================

BASE_URL="http://127.0.0.1:8000/api"
LAT=24.7136
LNG=46.6753

echo "========================================"
echo "اختبار API البحث عن أقرب الشيفات"
echo "========================================"
echo ""

# Test 1: بحث أساسي
echo "Test 1: بحث أساسي عن أقرب الشيفات"
echo "----------------------------------------"
curl -s "${BASE_URL}/chefs/nearest?lat=${LAT}&lng=${LNG}&radius=100" \
  -H "Accept: application/json" | jq '.data.data[] | {name, rating_avg, distance_km, distance_text}' | head -20
echo ""

# Test 2: بحث نصي
echo "Test 2: بحث نصي (search=شيف)"
echo "----------------------------------------"
curl -s "${BASE_URL}/chefs/nearest?lat=${LAT}&lng=${LNG}&radius=100&search=شيف" \
  -H "Accept: application/json" | jq '.data.data[] | {name, distance_text}' | head -10
echo ""

# Test 3: فلتر حسب التصنيف
echo "Test 3: فلتر حسب التصنيف (category_id=1)"
echo "----------------------------------------"
curl -s "${BASE_URL}/chefs/nearest?lat=${LAT}&lng=${LNG}&radius=100&category_id=1" \
  -H "Accept: application/json" | jq '.data.data[] | {name, distance_text}' | head -10
echo ""

# Test 4: فلتر حسب التقييم
echo "Test 4: فلتر حسب التقييم (min_rating=4)"
echo "----------------------------------------"
curl -s "${BASE_URL}/chefs/nearest?lat=${LAT}&lng=${LNG}&radius=100&min_rating=4" \
  -H "Accept: application/json" | jq '.data.data[] | {name, rating_avg, distance_text}' | head -10
echo ""

# Test 5: فلاتر متعددة
echo "Test 5: فلاتر متعددة"
echo "----------------------------------------"
curl -s "${BASE_URL}/chefs/nearest?lat=${LAT}&lng=${LNG}&radius=50&min_rating=3&max_price=300" \
  -H "Accept: application/json" | jq '.data.data[] | {name, rating_avg, base_hourly_rate, distance_text}' | head -15
echo ""

echo "========================================"
echo "انتهى الاختبار"
echo "========================================"
