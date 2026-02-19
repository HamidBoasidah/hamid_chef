# إصلاح خطأ التحقق من المبلغ مع كود الخصم

## 🐛 الخطأ

```json
{
    "message": "booking.validation.total_amount_mismatch",
    "errors": {
        "total_amount": ["booking.validation.total_amount_mismatch"]
    }
}
```

## 🔍 السبب

دالة `validatePricing` في `StoreBookingRequest` كانت تتحقق من أن:

```php
total_amount = (unit_price × hours_count) + extra_guests_amount
```

لكنها **لم تأخذ في الاعتبار كود الخصم**!

### مثال على المشكلة:

```json
{
    "unit_price": 150,
    "hours_count": 3,
    "extra_guests_amount": 0,
    "discount_code_id": 3,
    "original_amount": 450,
    "discount_amount": 90,
    "total_amount": 360
}
```

**الحساب القديم (الخاطئ):**

```
expectedTotal = 150 × 3 + 0 = 450
total_amount = 360
450 ≠ 360 ❌ خطأ!
```

**الحساب الصحيح:**

```
baseAmount = 150 × 3 + 0 = 450
original_amount = 450 ✅
total_amount = 450 - 90 = 360 ✅
```

---

## ✅ الحل

تم تحديث دالة `validatePricing` لتتعامل مع حالتين:

### 1️⃣ **بدون كود خصم:**

```php
total_amount = (unit_price × hours_count) + extra_guests_amount
```

### 2️⃣ **مع كود خصم:**

```php
// التحقق من original_amount
original_amount = (unit_price × hours_count) + extra_guests_amount

// التحقق من total_amount
total_amount = original_amount - discount_amount
```

---

## 📝 الكود الجديد

```php
protected function validatePricing($validator)
{
    $unitPrice = $this->input('unit_price');
    $hoursCount = $this->input('hours_count');
    $extraGuestsAmount = $this->input('extra_guests_amount', 0);
    $totalAmount = $this->input('total_amount');
    $serviceType = $this->input('service_type');

    // Discount fields
    $discountCodeId = $this->input('discount_code_id');
    $discountAmount = $this->input('discount_amount', 0);
    $originalAmount = $this->input('original_amount');

    if ($unitPrice && $hoursCount && $totalAmount) {
        // Calculate base amount (before discount)
        $baseAmount = $serviceType === 'hourly'
            ? ($unitPrice * $hoursCount) + $extraGuestsAmount
            : $unitPrice + $extraGuestsAmount;

        // If discount code is used
        if ($discountCodeId && $discountAmount > 0) {
            // Validate original_amount matches base calculation
            if ($originalAmount && abs($baseAmount - $originalAmount) > 0.01) {
                $validator->errors()->add('original_amount',
                    __('booking.validation.original_amount_mismatch'));
            }

            // Validate total_amount = original_amount - discount_amount
            $expectedTotal = $originalAmount - $discountAmount;
            if (abs($expectedTotal - $totalAmount) > 0.01) {
                $validator->errors()->add('total_amount',
                    __('booking.validation.total_amount_mismatch'));
            }
        } else {
            // No discount: total_amount should equal base calculation
            if (abs($baseAmount - $totalAmount) > 0.01) {
                $validator->errors()->add('total_amount',
                    __('booking.validation.total_amount_mismatch'));
            }
        }
    }

    // Validate extra guests
    $extraGuestsCount = $this->input('extra_guests_count', 0);
    if ($extraGuestsCount > 0 && (!$extraGuestsAmount || $extraGuestsAmount <= 0)) {
        $validator->errors()->add('extra_guests_amount',
            __('booking.validation.extra_guests_amount_required'));
    }
}
```

---

## 🧪 أمثلة الاختبار

### ✅ مثال 1: بدون كود خصم

**Request:**

```json
{
    "chef_id": 1,
    "chef_service_id": 5,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 3,
    "number_of_guests": 10,
    "service_type": "hourly",
    "unit_price": 150,
    "total_amount": 450
}
```

**التحقق:**

```
baseAmount = 150 × 3 = 450
total_amount = 450
450 = 450 ✅ صحيح
```

---

### ✅ مثال 2: مع كود خصم

**Request:**

```json
{
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
}
```

**التحقق:**

```
baseAmount = 150 × 3 = 450
original_amount = 450
450 = 450 ✅ صحيح

expectedTotal = 450 - 90 = 360
total_amount = 360
360 = 360 ✅ صحيح
```

---

### ✅ مثال 3: مع ضيوف إضافيين وكود خصم

**Request:**

```json
{
    "chef_id": 1,
    "chef_service_id": 5,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 4,
    "number_of_guests": 15,
    "service_type": "hourly",
    "unit_price": 200,
    "extra_guests_count": 5,
    "extra_guests_amount": 100,
    "discount_code_id": 3,
    "original_amount": 900,
    "discount_amount": 180,
    "total_amount": 720
}
```

**التحقق:**

```
baseAmount = (200 × 4) + 100 = 900
original_amount = 900
900 = 900 ✅ صحيح

expectedTotal = 900 - 180 = 720
total_amount = 720
720 = 720 ✅ صحيح
```

---

### ❌ مثال 4: خطأ في الحساب

**Request:**

```json
{
    "unit_price": 150,
    "hours_count": 3,
    "discount_code_id": 3,
    "original_amount": 450,
    "discount_amount": 90,
    "total_amount": 400
}
```

**التحقق:**

```
expectedTotal = 450 - 90 = 360
total_amount = 400
360 ≠ 400 ❌ خطأ!
```

**Response:**

```json
{
    "message": "booking.validation.total_amount_mismatch",
    "errors": {
        "total_amount": ["booking.validation.total_amount_mismatch"]
    }
}
```

---

### ❌ مثال 5: خطأ في original_amount

**Request:**

```json
{
    "unit_price": 150,
    "hours_count": 3,
    "discount_code_id": 3,
    "original_amount": 500,
    "discount_amount": 90,
    "total_amount": 410
}
```

**التحقق:**

```
baseAmount = 150 × 3 = 450
original_amount = 500
450 ≠ 500 ❌ خطأ!
```

**Response:**

```json
{
    "message": "booking.validation.original_amount_mismatch",
    "errors": {
        "original_amount": ["booking.validation.original_amount_mismatch"]
    }
}
```

---

## 📊 جدول التحقق

| الحالة       | الحقول المطلوبة                                                                                       | التحقق                                                                                 |
| ------------ | ----------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------- |
| **بدون خصم** | `unit_price`, `hours_count`, `total_amount`                                                           | `total_amount = baseAmount`                                                            |
| **مع خصم**   | `unit_price`, `hours_count`, `discount_code_id`, `original_amount`, `discount_amount`, `total_amount` | `original_amount = baseAmount` <br> `total_amount = original_amount - discount_amount` |

---

## 💡 نصائح مهمة

### 1. **احسب المبلغ الأصلي أولاً**

```javascript
// Frontend calculation
const baseAmount =
    serviceType === "hourly"
        ? unitPrice * hoursCount + extraGuestsAmount
        : unitPrice + extraGuestsAmount;

const originalAmount = baseAmount;
```

### 2. **طبق الخصم على المبلغ الأصلي**

```javascript
if (discountCode) {
    const validation = await validateDiscountCode(discountCode, originalAmount);

    if (validation.valid) {
        bookingData.discount_code_id = validation.discount_code_id;
        bookingData.original_amount = originalAmount;
        bookingData.discount_amount = validation.discount_amount;
        bookingData.total_amount = originalAmount - validation.discount_amount;
    }
} else {
    bookingData.total_amount = originalAmount;
}
```

### 3. **تأكد من الدقة في الحسابات**

```javascript
// استخدم toFixed(2) لتجنب مشاكل الأرقام العشرية
const total_amount = parseFloat((originalAmount - discountAmount).toFixed(2));
```

---

## 🔗 ملفات ذات صلة

- **BOOKING_WITH_DISCOUNT_GUIDE_AR.md** - دليل استخدام أكواد الخصم
- **BOOKING_DTO_FIX_AR.md** - إصلاح خطأ BookingDTO
- **app/Http/Requests/StoreBookingRequest.php** - ملف الـ Request

---

## ✅ Checklist

- [x] تحديث `validatePricing()` للتعامل مع الخصم
- [x] التحقق من `original_amount` عند وجود خصم
- [x] التحقق من `total_amount = original_amount - discount_amount`
- [x] الحفاظ على التوافق مع الحجوزات بدون خصم

---

**تاريخ الإصلاح:** 2 فبراير 2026
**الحالة:** ✅ تم الإصلاح
