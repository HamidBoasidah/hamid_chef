# دليل استخدام أكواد الخصم مع البوكينج 🎫

## نظرة عامة

بعد إضافة نظام أكواد الخصم، أصبح بإمكانك إرسال كود خصم مع طلب الحجز (Booking).

---

## 📋 ما الذي تغير في البوكينج؟

### الحقول الجديدة في جدول `bookings`:

```sql
discount_code_id       -- معرف كود الخصم (اختياري)
original_amount        -- المبلغ الأصلي قبل الخصم
discount_amount        -- قيمة الخصم
```

### الحقول الموجودة مسبقاً:

```sql
total_amount           -- المبلغ النهائي بعد الخصم
```

---

## 🔄 سير العمل (Workflow)

### 1️⃣ **بدون كود خصم** (كما كان سابقاً)

```
المبلغ الأصلي = total_amount
لا يوجد خصم
```

### 2️⃣ **مع كود خصم** (الجديد)

```
1. العميل يدخل كود الخصم
2. التحقق من صحة الكود عبر API
3. حساب الخصم
4. إرسال البيانات مع الحجز
5. تسجيل استخدام الكود
```

---

## 📡 API Endpoints

### 1. التحقق من كود الخصم (خطوة اختيارية قبل الحجز)

**Endpoint:**

```
POST /api/discount-codes/validate
```

**Headers:**

```json
{
    "Authorization": "Bearer YOUR_TOKEN",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
```

**Request Body:**

```json
{
    "code": "SUMMER2026",
    "amount": 500
}
```

**Response (نجاح):**

```json
{
    "success": true,
    "data": {
        "valid": true,
        "discount_code_id": 3,
        "code": "SUMMER2026",
        "type": "percentage",
        "value": "20.00",
        "original_amount": 500,
        "discount_amount": 100,
        "final_amount": 400
    },
    "message": "الكود صالح"
}
```

**Response (فشل - مبلغ أقل من الحد الأدنى):**

```json
{
    "success": false,
    "message": "الحد الأدنى للطلب هو 100 ريال"
}
```

**Response (فشل - كود غير موجود):**

```json
{
    "success": false,
    "message": "الكود غير موجود"
}
```

---

### 2. إنشاء حجز (مع أو بدون كود خصم)

**Endpoint:**

```
POST /api/bookings
```

**Headers:**

```json
{
    "Authorization": "Bearer YOUR_TOKEN",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
```

---

## 📝 Request Body للبوكينج

### أ) **بدون كود خصم** (كما كان سابقاً)

```json
{
    "chef_id": 1,
    "chef_service_id": 5,
    "address_id": 2,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 3,
    "number_of_guests": 10,
    "service_type": "hourly",
    "unit_price": 150,
    "extra_guests_count": 0,
    "extra_guests_amount": 0,
    "total_amount": 450,
    "commission_amount": 45,
    "notes": "حفلة عيد ميلاد"
}
```

**الحسابات:**

```
unit_price = 150 ريال/ساعة
hours_count = 3 ساعات
total_amount = 150 × 3 = 450 ريال
```

---

### ب) **مع كود خصم** (الجديد) ✨

```json
{
    "chef_id": 1,
    "chef_service_id": 5,
    "address_id": 2,
    "date": "2026-02-15",
    "start_time": "18:00",
    "hours_count": 3,
    "number_of_guests": 10,
    "service_type": "hourly",
    "unit_price": 150,
    "extra_guests_count": 0,
    "extra_guests_amount": 0,
    "discount_code_id": 3,
    "original_amount": 450,
    "discount_amount": 90,
    "total_amount": 360,
    "commission_amount": 36,
    "notes": "حفلة عيد ميلاد"
}
```

**الحسابات:**

```
unit_price = 150 ريال/ساعة
hours_count = 3 ساعات
original_amount = 150 × 3 = 450 ريال

كود الخصم: SUMMER2026 (20%)
discount_amount = 450 × 20% = 90 ريال
total_amount = 450 - 90 = 360 ريال

commission_amount = 360 × 10% = 36 ريال
```

---

## 🔑 الحقول المطلوبة والاختيارية

### الحقول الأساسية (مطلوبة دائماً):

| الحقل              | النوع   | الوصف              | مثال                      |
| ------------------ | ------- | ------------------ | ------------------------- |
| `chef_id`          | integer | معرف الطاهي        | `1`                       |
| `chef_service_id`  | integer | معرف الخدمة        | `5`                       |
| `date`             | date    | تاريخ الحجز        | `"2026-02-15"`            |
| `start_time`       | time    | وقت البدء          | `"18:00"`                 |
| `hours_count`      | integer | عدد الساعات (1-12) | `3`                       |
| `number_of_guests` | integer | عدد الضيوف (1-50)  | `10`                      |
| `service_type`     | string  | نوع الخدمة         | `"hourly"` أو `"package"` |
| `unit_price`       | decimal | سعر الوحدة         | `150.00`                  |
| `total_amount`     | decimal | المبلغ النهائي     | `360.00`                  |

### الحقول الاختيارية:

| الحقل                 | النوع   | الوصف                 | متى تستخدمه؟                   |
| --------------------- | ------- | --------------------- | ------------------------------ |
| `address_id`          | integer | معرف العنوان          | إذا كانت الخدمة في موقع العميل |
| `extra_guests_count`  | integer | عدد الضيوف الإضافيين  | إذا تجاوز العدد الحد المسموح   |
| `extra_guests_amount` | decimal | رسوم الضيوف الإضافيين | مع `extra_guests_count`        |
| `commission_amount`   | decimal | عمولة المنصة          | يُحسب تلقائياً عادةً           |
| `notes`               | string  | ملاحظات               | أي تفاصيل إضافية               |

### الحقول الخاصة بكود الخصم (اختيارية):

| الحقل              | النوع   | الوصف            | متى تستخدمه؟          |
| ------------------ | ------- | ---------------- | --------------------- |
| `discount_code_id` | integer | معرف كود الخصم   | عند استخدام كود خصم   |
| `original_amount`  | decimal | المبلغ قبل الخصم | مع `discount_code_id` |
| `discount_amount`  | decimal | قيمة الخصم       | مع `discount_code_id` |

---

## 💡 أمثلة عملية

### مثال 1: حجز بسيط بدون خصم

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

---

### مثال 2: التحقق من كود خصم ثم الحجز

**الخطوة 1: التحقق من الكود**

```bash
curl -X POST "http://localhost/api/discount-codes/validate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SUMMER2026",
    "amount": 450
  }'
```

**Response:**

```json
{
    "success": true,
    "data": {
        "valid": true,
        "discount_code_id": 3,
        "discount_amount": 90,
        "final_amount": 360
    }
}
```

**الخطوة 2: إنشاء الحجز مع الخصم**

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

### مثال 3: حجز مع ضيوف إضافيين وكود خصم

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

**الحسابات:**

```
الخدمة الأساسية = 200 × 4 = 800 ريال
الضيوف الإضافيين = 100 ريال
المبلغ قبل الخصم = 800 + 100 = 900 ريال

كود الخصم 20%:
الخصم = 900 × 20% = 180 ريال
المبلغ النهائي = 900 - 180 = 720 ريال
```

---

## ⚠️ أخطاء شائعة وحلولها

### 1. "الحد الأدنى للطلب هو X ريال"

**السبب:** المبلغ أقل من الحد الأدنى المطلوب للكود.

**الحل:** تأكد أن `amount` في طلب التحقق أكبر من أو يساوي `min_order_amount` للكود.

---

### 2. "الكود غير موجود"

**السبب:** الكود المدخل غير صحيح أو غير موجود في قاعدة البيانات.

**الحل:** تحقق من صحة الكود أو استخدم كود آخر.

---

### 3. "لقد استخدمت هذا الكود من قبل"

**السبب:** المستخدم استنفد عدد الاستخدامات المسموح له.

**الحل:** استخدم كود خصم آخر.

---

### 4. "الكود منتهي الصلاحية"

**السبب:** تاريخ الكود انتهى.

**الحل:** استخدم كود خصم ساري المفعول.

---

### 5. "total_amount لا يتطابق مع الحسابات"

**السبب:** خطأ في حساب المبلغ النهائي.

**الحل:** تأكد من:

```
total_amount = original_amount - discount_amount
```

---

## 🎯 نصائح مهمة

### 1. **التحقق من الكود قبل الحجز (موصى به)**

```javascript
// Frontend Flow
async function createBookingWithDiscount(bookingData, discountCode) {
    // 1. التحقق من الكود أولاً
    const validation = await validateDiscountCode(
        discountCode,
        bookingData.total_amount,
    );

    if (validation.valid) {
        // 2. تحديث بيانات الحجز
        bookingData.discount_code_id = validation.discount_code_id;
        bookingData.original_amount = validation.original_amount;
        bookingData.discount_amount = validation.discount_amount;
        bookingData.total_amount = validation.final_amount;

        // 3. إنشاء الحجز
        return await createBooking(bookingData);
    } else {
        throw new Error(validation.message);
    }
}
```

---

### 2. **حساب العمولة بعد الخصم**

```javascript
// العمولة تُحسب على المبلغ النهائي (بعد الخصم)
commission_amount = total_amount × commission_rate
```

**مثال:**

```
original_amount = 500 ريال
discount_amount = 100 ريال
total_amount = 400 ريال
commission_rate = 10%

commission_amount = 400 × 10% = 40 ريال
```

---

### 3. **التعامل مع الأخطاء**

```javascript
try {
    const response = await fetch("/api/bookings", {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify(bookingData),
    });

    const result = await response.json();

    if (!result.success) {
        // عرض رسالة الخطأ للمستخدم
        alert(result.message);
    }
} catch (error) {
    console.error("خطأ في إنشاء الحجز:", error);
}
```

---

## 📊 ملخص التغييرات

### ما كان موجوداً (قبل):

```json
{
    "total_amount": 450
}
```

### ما أضيف (بعد):

```json
{
    "discount_code_id": 3,
    "original_amount": 450,
    "discount_amount": 90,
    "total_amount": 360
}
```

---

## ✅ Checklist للمطورين

عند إضافة كود خصم للبوكينج:

- [ ] التحقق من صحة الكود عبر `/api/discount-codes/validate`
- [ ] حفظ `discount_code_id` من الاستجابة
- [ ] تعيين `original_amount` (المبلغ قبل الخصم)
- [ ] تعيين `discount_amount` (قيمة الخصم)
- [ ] تحديث `total_amount` (المبلغ بعد الخصم)
- [ ] إعادة حساب `commission_amount` على المبلغ النهائي
- [ ] إرسال جميع الحقول في طلب الحجز

---

## 🔗 ملفات مرجعية

- **DISCOUNT_CODES_SYSTEM_COMPLETE.md** - نظرة شاملة على النظام
- **DISCOUNT_CODES_FINAL_IMPLEMENTATION_GUIDE.md** - دليل التنفيذ
- **postman/DISCOUNT_CODES_CURL_EXAMPLES.md** - أمثلة CURL
- **postman/discount_codes.postman_collection.json** - مجموعة Postman

---

## 📞 الدعم

إذا واجهت أي مشكلة:

1. تحقق من الـ logs في `storage/logs/laravel.log`
2. تأكد من أن الـ migrations تم تشغيلها
3. تحقق من وجود بيانات تجريبية في جدول `discount_codes`

---

**آخر تحديث:** 2 فبراير 2026
