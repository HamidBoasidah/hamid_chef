# إصلاح خطأ BookingDTO - ArgumentCountError

## 🐛 الخطأ

```
ArgumentCountError: Too few arguments to function App\DTOs\BookingDTO::__construct(),
23 passed but at least 27 expected
```

## 🔍 السبب

عند إضافة نظام أكواد الخصم، تم تحديث `BookingDTO` لإضافة 4 حقول جديدة:

1. `discount_code_id` - معرف كود الخصم
2. `discount_code` - كود الخصم (من العلاقة)
3. `discount_amount` - قيمة الخصم
4. `original_amount` - المبلغ الأصلي قبل الخصم

لكن لم يتم تحديث جميع الأماكن التي تستخدم `new BookingDTO()`.

---

## ✅ الحل

تم تحديث جميع الملفات التالية لتمرير 27 معامل بدلاً من 23:

### 1. `app/Http/Controllers/Api/BookingController.php`

**قبل:**

```php
$bookingDTO = new BookingDTO(
    null,
    $validated['customer_id'],
    // ... 21 معامل آخر
);
```

**بعد:**

```php
$bookingDTO = new BookingDTO(
    null, // id
    $validated['customer_id'], // customer_id
    // ... المعاملات الأصلية
    $validated['discount_code_id'] ?? null, // discount_code_id
    null, // discount_code
    $validated['discount_amount'] ?? 0, // discount_amount
    $validated['original_amount'] ?? $validated['total_amount'], // original_amount
    // ... باقي المعاملات
);
```

---

### 2. `app/Services/BookingService.php`

تم تحديث 3 أماكن في هذا الملف:

#### أ) دالة `create()`

#### ب) دالة `update()` - أول BookingDTO

#### ج) دالة `updateModel()` - ثاني BookingDTO

---

### 3. `app/Services/BookingConflictService.php`

**قبل:**

```php
$bookingDTO = new BookingDTO(
    null, null, $chefId, $serviceId, null, $date, $startTime, $hoursCount,
    null, null, null, null, null, null, null, null, null, null, true, null, null
);
```

**بعد:**

```php
$bookingDTO = new BookingDTO(
    null, // id
    null, // customer_id
    $chefId, // chef_id
    $serviceId, // chef_service_id
    null, // address_id
    $date, // date
    $startTime, // start_time
    $hoursCount, // hours_count
    null, // number_of_guests
    null, // service_type
    null, // unit_price
    null, // extra_guests_count
    null, // extra_guests_amount
    null, // total_amount
    null, // commission_amount
    null, // payment_status
    null, // booking_status
    null, // rejection_reason
    null, // cancellation_reason
    null, // discount_code_id
    null, // discount_code
    0, // discount_amount
    null, // original_amount
    null, // notes
    true, // is_active
    null, // created_by
    null // updated_by
);
```

---

### 4. `app/Http/Controllers/Admin/BookingController.php`

تم تحديثه بنفس الطريقة.

---

### 5. `app/Http/Requests/StoreBookingRequest.php`

تم إضافة الحقول الجديدة كحقول اختيارية:

```php
'rules' => [
    // ... الحقول الموجودة

    // Discount code fields (optional)
    'discount_code_id' => 'nullable|integer|exists:discount_codes,id',
    'discount_amount' => 'nullable|numeric|min:0|max:9999999.99',
    'original_amount' => 'nullable|numeric|min:0|max:9999999.99'
]
```

---

## 📊 ترتيب المعاملات في BookingDTO

```php
public function __construct(
    $id,                    // 1
    $customer_id,           // 2
    $chef_id,               // 3
    $chef_service_id,       // 4
    $address_id,            // 5
    $date,                  // 6
    $start_time,            // 7
    $hours_count,           // 8
    $number_of_guests,      // 9
    $service_type,          // 10
    $unit_price,            // 11
    $extra_guests_count,    // 12
    $extra_guests_amount,   // 13
    $total_amount,          // 14
    $commission_amount,     // 15
    $payment_status,        // 16
    $booking_status,        // 17
    $rejection_reason,      // 18 ⬅️ كان مفقود
    $cancellation_reason,   // 19 ⬅️ كان مفقود
    $discount_code_id,      // 20 ⬅️ جديد
    $discount_code,         // 21 ⬅️ جديد
    $discount_amount,       // 22 ⬅️ جديد
    $original_amount,       // 23 ⬅️ جديد
    $notes,                 // 24
    $is_active,             // 25
    $created_by,            // 26
    $updated_by,            // 27
    $created_at = null,     // اختياري
    $deleted_at = null      // اختياري
)
```

---

## 🧪 الاختبار

بعد الإصلاح، يجب أن يعمل إنشاء الحجز بدون أخطاء:

### بدون كود خصم:

```bash
curl -X POST "http://localhost/api/bookings" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "chef_id": 1,
    "chef_service_id": 5,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 3,
    "number_of_guests": 10,
    "service_type": "hourly",
    "unit_price": 150,
    "total_amount": 450
  }'
```

### مع كود خصم:

```bash
curl -X POST "http://localhost/api/bookings" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "chef_id": 1,
    "chef_service_id": 5,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 3,
    "number_of_guests": 10,
    "service_type": "hourly",
    "unit_price": 150,
    "discount_code_id": 3,
    "original_amount": 450,
    "discount_amount": 90,
    "total_amount": 360
  }'
```

---

## 📝 ملاحظات مهمة

### 1. الحقول الاختيارية

الحقول التالية **اختيارية** ولها قيم افتراضية:

- `discount_code_id` → `null`
- `discount_amount` → `0`
- `original_amount` → `total_amount` (إذا لم يتم تمريره)

### 2. التوافق مع الإصدارات السابقة

الكود الآن متوافق مع:

- ✅ الحجوزات القديمة (بدون خصم)
- ✅ الحجوزات الجديدة (مع أو بدون خصم)

### 3. القيم الافتراضية

عند عدم تمرير حقول الخصم:

```php
'discount_code_id' => null
'discount_amount' => 0
'original_amount' => $total_amount
```

---

## ✅ Checklist للتحقق

- [x] تحديث `BookingController` (API)
- [x] تحديث `BookingController` (Admin)
- [x] تحديث `BookingService::create()`
- [x] تحديث `BookingService::update()`
- [x] تحديث `BookingService::updateModel()`
- [x] تحديث `BookingConflictService::checkChefAvailability()`
- [x] إضافة حقول الخصم إلى `StoreBookingRequest`

---

## 🔗 ملفات ذات صلة

- **BOOKING_WITH_DISCOUNT_GUIDE_AR.md** - دليل استخدام أكواد الخصم
- **DISCOUNT_CODES_SYSTEM_COMPLETE.md** - نظام أكواد الخصم الكامل
- **app/DTOs/BookingDTO.php** - تعريف الـ DTO

---

**تاريخ الإصلاح:** 2 فبراير 2026
**الحالة:** ✅ تم الإصلاح
