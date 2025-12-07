# مثال كامل - AddressController بالطريقة الصحيحة

## 📝 AddressController المحسّن

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\AddressService;
use App\DTOs\AddressDTO;
use App\Http\Traits\SuccessResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use SuccessResponse;

    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Get all addresses for authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Query 1: جلب كل العناوين للمستخدم مع العلاقات المطلوبة
        $addresses = $this->addressService->getAllForUser(
            auth()->id(),
            ['governorate', 'district', 'area']
        );

        return $this->resourceResponse(
            $addresses->map(fn($address) => AddressDTO::fromModel($address)->toIndexArray()),
            'تم جلب قائمة العناوين بنجاح'
        );
    }

    /**
     * Show a specific address
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Query 1: جلب العنوان + التحقق من الملكية
        $address = $this->addressService->findForUser(
            $id,
            auth()->id(),
            ['governorate', 'district', 'area']
        );

        // Optional: استخدام Policy للتحقق من الصلاحيات
        $this->authorize('view', $address);

        return $this->resourceResponse(
            AddressDTO::fromModel($address)->toArray(),
            'تم جلب العنوان بنجاح'
        );
    }

    /**
     * Create a new address
     * 
     * @param StoreAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Query 1: إنشاء العنوان
        $address = $this->addressService->create($data);

        return $this->createdResponse(
            AddressDTO::fromModel($address)->toArray(),
            'تم إضافة العنوان بنجاح'
        );
    }

    /**
     * Update an existing address
     * 
     * @param int $id
     * @param UpdateAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateAddressRequest $request)
    {
        // Query 1: جلب العنوان + التحقق من الملكية ✅
        $address = $this->addressService->findForUser(
            $id,
            auth()->id(),
            ['governorate', 'district', 'area']
        );

        // Optional: استخدام Policy
        $this->authorize('update', $address);

        // Query 2: التحديث فقط (بدون جلب مرة ثانية) ✅
        $updated = $this->addressService->updateModel(
            $address,
            $request->validated()
        );

        return $this->updatedResponse(
            AddressDTO::fromModel($updated)->toArray(),
            'تم تحديث العنوان بنجاح'
        );
    }

    /**
     * Delete an address
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Query 1: جلب العنوان + التحقق من الملكية
        $address = $this->addressService->findForUser($id, auth()->id());

        // Optional: استخدام Policy
        $this->authorize('delete', $address);

        // Query 2: الحذف
        $this->addressService->delete($address->id);

        return $this->deletedResponse('تم حذف العنوان بنجاح');
    }

    /**
     * Activate an address
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate($id)
    {
        // Query 1: جلب العنوان + التحقق من الملكية
        $address = $this->addressService->findForUser($id, auth()->id());

        // Optional: استخدام Policy
        $this->authorize('activate', $address);

        // Query 2: التفعيل
        $activated = $this->addressService->activate($address->id);

        return $this->updatedResponse(
            AddressDTO::fromModel($activated)->toArray(),
            'تم تفعيل العنوان بنجاح'
        );
    }

    /**
     * Deactivate an address
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate($id)
    {
        // Query 1: جلب العنوان + التحقق من الملكية
        $address = $this->addressService->findForUser($id, auth()->id());

        // Optional: استخدام Policy
        $this->authorize('deactivate', $address);

        // Query 2: إلغاء التفعيل
        $deactivated = $this->addressService->deactivate($address->id);

        return $this->updatedResponse(
            AddressDTO::fromModel($deactivated)->toArray(),
            'تم إلغاء تفعيل العنوان بنجاح'
        );
    }

    /**
     * Set address as default
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAsDefault($id)
    {
        // Query 1: جلب العنوان + التحقق من الملكية
        $address = $this->addressService->findForUser($id, auth()->id());

        // Optional: استخدام Policy
        $this->authorize('setAsDefault', $address);

        // Query 2: إلغاء Default من كل العناوين
        // Query 3: تعيين العنوان الحالي كـ Default
        $this->addressService->setAsDefault($address, auth()->id());

        return $this->updatedResponse(
            AddressDTO::fromModel($address->fresh())->toArray(),
            'تم تعيين العنوان كافتراضي بنجاح'
        );
    }
}
```

---

## 🎯 النقاط المهمة

### 1. استخدام `findForUser()` دائماً
```php
// ✅ صح
$address = $this->addressService->findForUser($id, auth()->id());

// ❌ خطأ
$address = $this->addressService->find($id);
if ($address->user_id !== auth()->id()) { ... }
```

### 2. استخدام `updateModel()` للتحديث
```php
// ✅ صح: استخدام الموديل الموجود
$address = $this->addressService->findForUser($id, auth()->id());
$updated = $this->addressService->updateModel($address, $data);

// ❌ خطأ: جلب العنوان مرة ثانية
$address = $this->addressService->findForUser($id, auth()->id());
$updated = $this->addressService->update($id, $data);  // يجلب مرة ثانية!
```

### 3. Eager Loading حسب الحاجة
```php
// في index: نحتاج معلومات أساسية فقط
$addresses = $this->addressService->getAllForUser(auth()->id(), [
    'governorate:id,name_ar'
]);

// في show: نحتاج كل التفاصيل
$address = $this->addressService->findForUser($id, auth()->id(), [
    'governorate', 'district', 'area'
]);
```

---

## 📊 مقارنة الأداء لكل Method

| Method | Queries (القديمة) | Queries (الجديدة) | التحسين |
|--------|-------------------|-------------------|---------|
| index | 1 + N | 1 | ✅ |
| show | 1 | 1 | ✅ |
| store | 1 | 1 | ✅ |
| update | 3 | 2 | ✅ 33% |
| destroy | 2 | 2 | ✅ |
| activate | 3 | 2 | ✅ 33% |
| deactivate | 3 | 2 | ✅ 33% |
| setAsDefault | 4 | 3 | ✅ 25% |

---

## 🔐 إضافة AddressPolicy (Optional)

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;

class AddressPolicy
{
    public function view(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    public function update(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    public function activate(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    public function deactivate(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    public function setAsDefault(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }
}
```

تسجيل الـ Policy في `AuthServiceProvider`:
```php
protected $policies = [
    Address::class => AddressPolicy::class,
];
```

---

## ✅ الخلاصة

**التحسينات الرئيسية:**
1. ✅ استخدام `findForUser()` بدلاً من `find()` + manual check
2. ✅ استخدام `updateModel()` بدلاً من `update($id)`
3. ✅ Eager Loading مرن حسب الحاجة
4. ✅ استخدام Policies للتحقق من الصلاحيات
5. ✅ تقليل Database Queries بنسبة 25-33%

**الكود القديم:**
- لسه شغال للتوافق
- يعطي نفس النتيجة
- لكن أبطأ شوية

**الكود الجديد:**
- أسرع بنسبة 25-33%
- أنظف وأسهل في القراءة
- أكثر أماناً مع Policies
