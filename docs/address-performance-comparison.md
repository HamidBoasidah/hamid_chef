# مقارنة الأداء - Address Update

## ❌ الطريقة القديمة (3 Queries)

```php
// في AddressController
public function update($id, UpdateAddressRequest $request, AddressService $service)
{
    // Query 1: جلب العنوان للتحقق من الملكية
    $address = $service->find($id);
    
    // التحقق اليدوي من الملكية
    if ($address->user_id !== auth()->id()) {
        abort(403, 'غير مصرح لك بتعديل هذا العنوان');
    }
    
    // Query 2: جلب العنوان مرة ثانية (في Service)
    // Query 3: التحديث
    $updated = $service->update($id, $request->validated());
    
    return response()->json([
        'success' => true,
        'data' => AddressDTO::fromModel($updated)->toArray()
    ]);
}

// في AddressService
public function update($id, array $attributes)
{
    $address = $this->addresses->findOrFail($id);  // Query 2 ❌
    return $this->addresses->update($address, $attributes);  // Query 3
}
```

**المشكلة**: جلبنا العنوان **مرتين** - مرة في Controller ومرة في Service!

---

## ⚠️ المحاولة الأولى (لسه 3 Queries)

```php
// في AddressService
public function update($id, array $attributes)
{
    $address = $this->addresses->findOrFail($id);  // Query 2 ❌
    return $this->addresses->update($address, $attributes);  // Query 3
}
```

**المشكلة**: فقط نقلنا الـ `findOrFail` من BaseRepository إلى Service، لكن لسه بنجلب العنوان مرتين!

---

## ✅ الحل الصحيح (2 Queries فقط)

### الخطوة 1: إضافة Methods جديدة في Service

```php
// في AddressService
class AddressService
{
    /**
     * Find address for a specific user
     * Combines find + ownership check in one query
     */
    public function findForUser(int $id, int $userId, array $with = []): Address
    {
        return $this->addresses->findByIdAndUser($id, $userId, $with);
    }

    /**
     * Update an existing address model (preferred method)
     * This avoids redundant database queries
     */
    public function updateModel(Address $address, array $attributes): Address
    {
        return $this->addresses->update($address, $attributes);
    }

    /**
     * Update address by ID (backward compatible)
     * @deprecated Use updateModel() instead
     */
    public function update($id, array $attributes)
    {
        $address = $this->addresses->findOrFail($id);
        return $this->addresses->update($address, $attributes);
    }
}
```

### الخطوة 2: إضافة Methods في Repository

```php
// في AddressRepository
class AddressRepository extends BaseRepository
{
    /**
     * Find address by ID that belongs to a specific user
     */
    public function findByIdAndUser(int $id, int $userId, array $with = []): Address
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->with($with)
            ->firstOrFail();
    }
}
```

### الخطوة 3: استخدام الطريقة الجديدة في Controller

```php
// في AddressController
public function update($id, UpdateAddressRequest $request, AddressService $service)
{
    // Query 1: جلب العنوان + التحقق من الملكية في query واحد ✅
    $address = $service->findForUser($id, auth()->id(), [
        'governorate', 'district', 'area'
    ]);
    
    // استخدام Policy للتحقق من الصلاحيات (optional)
    $this->authorize('update', $address);
    
    // Query 2: التحديث فقط (بدون جلب مرة ثانية) ✅
    $updated = $service->updateModel($address, $request->validated());
    
    return response()->json([
        'success' => true,
        'data' => AddressDTO::fromModel($updated)->toArray()
    ]);
}
```

---

## 📊 مقارنة الأداء

| الطريقة | Query 1 | Query 2 | Query 3 | المجموع |
|---------|---------|---------|---------|---------|
| **القديمة** | find($id) | findOrFail($id) | update() | **3 Queries** |
| **المحاولة الأولى** | find($id) | findOrFail($id) | update() | **3 Queries** ❌ |
| **الصحيحة** | findForUser() | update() | - | **2 Queries** ✅ |

**التحسين**: تقليل 33% من Database Queries

---

## 🔑 النقاط المهمة

### 1. استخدام الموديل الموجود
```php
// ❌ خطأ: جلب العنوان مرتين
$address = $service->find($id);
$updated = $service->update($id, $data);  // يجلب العنوان مرة ثانية!

// ✅ صح: استخدام الموديل الموجود
$address = $service->findForUser($id, auth()->id());
$updated = $service->updateModel($address, $data);  // يستخدم نفس الموديل
```

### 2. دمج العمليات
```php
// ❌ خطأ: عمليتين منفصلتين
$address = Address::where('id', $id)->first();  // Query 1
if ($address->user_id !== auth()->id()) { ... }

// ✅ صح: عملية واحدة
$address = Address::where('id', $id)
    ->where('user_id', auth()->id())
    ->firstOrFail();  // Query 1 فقط
```

### 3. Eager Loading مرن
```php
// ❌ خطأ: تحميل كل العلاقات دائماً
protected $with = ['governorate', 'district', 'area'];

// ✅ صح: تحميل حسب الحاجة
$address = $service->findForUser($id, auth()->id(), [
    'governorate', 'district'  // فقط ما نحتاجه
]);
```

---

## 🎯 الخلاصة

**المشكلة الأساسية**: كنا نجلب نفس السجل **مرتين**
- مرة في Controller للتحقق من الملكية
- مرة في Service للتحديث

**الحل**: 
1. استخدام `findForUser()` للجلب + التحقق في query واحد
2. استخدام `updateModel()` لتحديث الموديل الموجود بدون جلب مرة ثانية
3. الكود القديم لسه شغال للتوافق

**النتيجة**: تقليل من 3 queries إلى 2 queries = **تحسين 33%** 🚀
