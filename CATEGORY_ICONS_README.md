# ميزة أيقونات SVG للأقسام

## نظرة عامة
تم إضافة ميزة جديدة تتيح للمديرين رفع أيقونات SVG للأقسام في النظام.

## الميزات المضافة

### 1. قاعدة البيانات
- إضافة عمود `icon_path` إلى جدول `categories`
- Migration: `2025_12_23_080540_add_icon_path_to_categories_table.php`

### 2. النماذج والخدمات
- تحديث `Category` model مع accessor للأيقونة (`getIconUrlAttribute`)
- تحديث `CategoryDTO` لدعم الأيقونات
- إنشاء `SVGIconService` للتعامل مع رفع وإدارة الأيقونات
- تحديث `CategoryService` لإدارة الأيقونات
- إنشاء `CategoryObserver` لحذف الأيقونات تلقائياً عند حذف الأقسام

### 3. Controllers والـ Routes
- تحديث `CategoryController` (API) مع endpoints جديدة
- تحديث `CategoryController` (Admin) مع وظائف إدارة الأيقونات
- إضافة `UploadCategoryIconRequest` للتحقق من صحة الملفات

### 4. الواجهات الأمامية
- تحديث صفحة تعديل الأقسام لإضافة قسم إدارة الأيقونات
- تحديث قائمة الأقسام لعرض الأيقونات
- إضافة ملفات الترجمة العربية والإنجليزية

## كيفية الاستخدام

### واجهة الإدارة
1. اذهب إلى قائمة الأقسام في لوحة الإدارة
2. اختر "تعديل" لأي قسم
3. في قسم "إدارة الأيقونة"، يمكنك:
   - رفع أيقونة جديدة (SVG فقط، أقل من 100KB)
   - حذف الأيقونة الحالية
   - عرض الأيقونة الحالية

### API Endpoints

#### رفع أيقونة
```http
POST /api/categories/{id}/icon
Content-Type: multipart/form-data

icon: [SVG file]
```

#### حذف أيقونة
```http
DELETE /api/categories/{id}/icon
```

#### عرض الأقسام مع الأيقونات
```http
GET /api/categories
GET /api/categories/{id}
```

### Admin Routes

#### رفع أيقونة
```http
POST /admin/categories/{id}/icon
```

#### حذف أيقونة
```http
DELETE /admin/categories/{id}/icon
```

## الأمان والتحقق

### التحقق من الملفات
- نوع الملف: SVG فقط
- حجم الملف: أقل من 100KB
- محتوى الملف: XML صالح
- فحص الأمان: منع JavaScript والعناصر الخطيرة

### التسجيل
- تسجيل جميع عمليات رفع وحذف الأيقونات
- تسجيل محاولات رفع الملفات المشبوهة
- تسجيل الأخطاء والعمليات الناجحة

## هيكل الملفات

### الملفات المضافة/المحدثة
```
app/
├── Services/SVGIconService.php (جديد)
├── Observers/CategoryObserver.php (جديد)
├── Http/Requests/UploadCategoryIconRequest.php (جديد)
├── Models/Category.php (محدث)
├── DTOs/CategoryDTO.php (محدث)
├── Services/CategoryService.php (محدث)
├── Http/Controllers/Api/CategoryController.php (محدث)
├── Http/Controllers/Admin/CategoryController.php (محدث)
└── Providers/
    ├── AppServiceProvider.php (محدث)
    └── RepositoryServiceProvider.php (محدث)

database/migrations/
└── 2025_12_23_080540_add_icon_path_to_categories_table.php (جديد)

resources/
├── js/Components/admin/category/
│   ├── EditCategory.vue (محدث)
│   └── ShowCategories.vue (محدث)
└── lang/
    ├── ar/categories.php (جديد)
    └── en/categories.php (جديد)

routes/
├── api.php (محدث)
└── admin.php (محدث)
```

### مجلد التخزين
```
storage/app/public/
└── category-icons/
    ├── category_1_1640995200_abc12345.svg
    ├── category_2_1640995300_def67890.svg
    └── ...
```

## اختبار الميزة

### اختبار رفع الأيقونة
1. قم بإنشاء ملف SVG بسيط
2. اذهب إلى تعديل أي قسم في لوحة الإدارة
3. ارفع الأيقونة
4. تحقق من ظهورها في قائمة الأقسام

### اختبار API
```bash
# رفع أيقونة
curl -X POST /api/categories/1/icon \
  -H "Authorization: Bearer {token}" \
  -F "icon=@test-icon.svg"

# عرض الأقسام
curl /api/categories
```

## ملاحظات مهمة
- الأيقونات يتم حذفها تلقائياً عند حذف القسم
- يتم استبدال الأيقونة القديمة عند رفع أيقونة جديدة
- جميع العمليات مسجلة في logs للمراجعة
- الميزة تدعم الواجهة العربية والإنجليزية