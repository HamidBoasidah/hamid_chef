# تحسين Authorization في AddressController

## ❌ الطريقة القديمة (قبل)

كان Authorization يتكرر في كل method:

```php
public function show($id, AddressService $addressService, Request $request)
{
    $address = $addressService->findForUser($id, $request->user()->id);
    
    // تكرار في كل method ❌
    $this->authorize('view', $address);
    
    return $this->resourceResponse(...);
}

public function update(UpdateAddressRequest $request, AddressService $addressService, $id)
{
    $address = $addressService->findForUser($id, $request->user()->id);
    
    // تكرار في كل method ❌
    $this->authorize('update', $address);
    
    $updated = $addressService->updateModel($address, $data);
    return $this->updatedResponse(...);
}

public function destroy(AddressService $addressService, Request $request, $id)
{
    $address = $addressService->findForUser($id, $request->user()->id);
    
    // تكرار في كل method ❌
    $this->authorize('delete', $address);
    
    $addressService->delete($id);
    return $this->deletedResponse(...);
}
```

**المشاكل:**
- ❌ تكرار `$this->authorize()` في كل method
- ❌ سهل نسيان إضافته في method جديد
- ❌ كود مكرر وغير DRY

---

## ✅ الطريقة الجديدة (بعد)

استخدام `authorizeResource()` في الـ `__construct`:

```php
public function __construct()
{
    $this->middleware('auth:sanctum');
    
    // Authorization تلقائي لكل resource methods ✅
    $this->authorizeResource(\App\Models\Address::class, 'address');
}

public function show(AddressService $addressService, Request $request, $id)
{
    // لا حاجة لـ authorize() - يتم تلقائياً ✅
    $address = $addressService->findForUser($id, $request->user()->id, [
        'governorate', 'district', 'area'
    ]);
    
    return $this->resourceResponse(...);
}

public function update(UpdateAddressRequest $request, AddressService $addressService, $id)
{
    // لا حاجة لـ authorize() - يتم تلقائياً ✅
    $address = $addressService->findForUser($id, $request->user()->id);
    
    $updated = $addressService->updateModel($address, $data);
    return $this->updatedResponse(...);
}

public function destroy(AddressService $addressService, Request $request, $id)
{
    // لا حاجة لـ authorize() - يتم تلقائياً ✅
    $address = $addressService->findForUser($id, $request->user()->id);
    
    $addressService->delete($id);
    return $this->deletedResponse(...);
}
```

**الفوائد:**
- ✅ Authorization تلقائي لكل resource methods
- ✅ لا تكرار في الكود
- ✅ مستحيل نسيان authorization
- ✅ كود أنظف وأسهل في الصيانة

---

## 📋 كيف يعمل `authorizeResource()`

عند استخدام `authorizeResource()` في الـ `__construct`:

```php
$this->authorizeResource(\App\Models\Address::class, 'address');
```

Laravel تلقائياً يربط كل method بـ Policy method:

| Controller Method | Policy Method | متى يتم التحقق |
|------------------|---------------|----------------|
| `index()` | `viewAny()` | قبل تنفيذ index |
| `store()` | `create()` | قبل تنفيذ store |
| `show()` | `view()` | قبل تنفيذ show |
| `update()` | `update()` | قبل تنفيذ update |
| `destroy()` | `delete()` | قبل تنفيذ destroy |

---

## 🔧 Custom Actions

للـ methods المخصصة (مثل `activate`, `deactivate`)، نحتاج authorization يدوي:

```php
public function activate(AddressService $addressService, Request $request, $id)
{
    $address = $addressService->findForUser($id, $request->user()->id);
    
    // Custom action - needs explicit authorization ✅
    $this->authorize('activate', $address);
    
    $activated = $addressService->activate($id);
    return $this->activatedResponse(...);
}

public function deactivate(AddressService $addressService, Request $request, $id)
{
    $address = $addressService->findForUser($id, $request->user()->id);
    
    // Custom action - needs explicit authorization ✅
    $this->authorize('deactivate', $address);
    
    $deactivated = $addressService->deactivate($id);
    return $this->deactivatedResponse(...);
}
```

---

## 📝 AddressPolicy المحدث

تم إضافة `viewAny()` و `create()`:

```php
class AddressPolicy
{
    /**
     * Determine if the user can view any addresses.
     */
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their own addresses
    }

    /**
     * Determine if the user can create addresses.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create addresses
    }

    /**
     * Determine if the user can view the address.
     */
    public function view(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Determine if the user can update the address.
     */
    public function update(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the address.
     */
    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Determine if the user can activate the address.
     */
    public function activate(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Determine if the user can deactivate the address.
     */
    public function deactivate(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Determine if the user can set the address as default.
     */
    public function setAsDefault(User $user, Address $address): bool
    {
        return $address->user_id === $user->id;
    }
}
```

---

## 📊 مقارنة الكود

### قبل
```php
// 5 methods × 1 line authorization = 5 lines
public function show(...) {
    $this->authorize('view', $address);
}
public function update(...) {
    $this->authorize('update', $address);
}
public function destroy(...) {
    $this->authorize('delete', $address);
}
public function activate(...) {
    $this->authorize('activate', $address);
}
public function deactivate(...) {
    $this->authorize('deactivate', $address);
}
```

### بعد
```php
// 1 line في __construct + 2 lines للـ custom actions = 3 lines
public function __construct() {
    $this->authorizeResource(\App\Models\Address::class, 'address');
}

// فقط للـ custom actions
public function activate(...) {
    $this->authorize('activate', $address);
}
public function deactivate(...) {
    $this->authorize('deactivate', $address);
}
```

**النتيجة**: تقليل 40% من كود Authorization! 🎉

---

## ✅ الخلاصة

**التحسينات:**
1. ✅ استخدام `authorizeResource()` في `__construct`
2. ✅ Authorization تلقائي لكل resource methods
3. ✅ إضافة `viewAny()` و `create()` في Policy
4. ✅ Authorization يدوي فقط للـ custom actions
5. ✅ كود أنظف بنسبة 40%

**الفوائد:**
- أسهل في الصيانة
- أقل احتمالية للأخطاء
- يتبع Laravel best practices
- DRY (Don't Repeat Yourself)
