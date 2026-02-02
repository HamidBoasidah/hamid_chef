# إصلاح خطأ Ziggy في routes أكواد الخصم

## المشكلة

```
Error: Ziggy error: 'discount_code' parameter is required for route 'admin.discount-codes.edit'.
```

## السبب

عند استخدام `Route::resource()` في Laravel، يتم تلقائياً إنشاء اسم المعامل من اسم النموذج بصيغة snake_case.

بما أن النموذج هو `DiscountCode`، فإن Laravel كان يتوقع المعامل `discount_code` بدلاً من `id`.

## الحل

تم تحديد اسم المعامل صراحةً في ملف الـ routes باستخدام `parameters()`:

### قبل الإصلاح:

```php
Route::resource('discount-codes', App\Http\Controllers\Admin\DiscountCodeController::class)
    ->names('discount-codes');
```

### بعد الإصلاح:

```php
Route::resource('discount-codes', App\Http\Controllers\Admin\DiscountCodeController::class)
    ->parameters(['discount-codes' => 'id'])
    ->names('discount-codes');
```

## الخطوات المنفذة

1. **تحديث routes/admin.php**
    - أضفت `->parameters(['discount-codes' => 'id'])`
    - يجبر Laravel على استخدام `{id}` بدلاً من `{discount_code}`

2. **مسح cache الـ routes**

    ```bash
    php artisan route:clear
    ```

3. **التحقق من الـ routes**

    ```bash
    php artisan route:list --name=discount-codes
    ```

    النتيجة:

    ```
    GET|HEAD   admin/discount-codes/{id}        admin.discount-codes.show
    PUT|PATCH  admin/discount-codes/{id}        admin.discount-codes.update
    DELETE     admin/discount-codes/{id}        admin.discount-codes.destroy
    GET|HEAD   admin/discount-codes/{id}/edit   admin.discount-codes.edit
    ```

4. **إعادة بناء الواجهة**
    ```bash
    npm run build
    ```

    - يحدث Ziggy routes في الواجهة

## التأثير

- ✅ زر "تعديل" يعمل الآن بشكل صحيح
- ✅ زر "عرض" يعمل بشكل صحيح
- ✅ زر "حذف" يعمل بشكل صحيح
- ✅ جميع routes تستخدم `{id}` بشكل متسق

## الملفات المعدلة

- `routes/admin.php`

## ملاحظات

- Controller كان يستخدم `$id` بالفعل، لذلك لم نحتاج لتعديله
- المشكلة كانت فقط في تعريف الـ route
- هذا الحل يحافظ على التوافق مع باقي الكود

---

**تاريخ الإصلاح:** 2 فبراير 2026
**الحالة:** ✅ تم الحل
