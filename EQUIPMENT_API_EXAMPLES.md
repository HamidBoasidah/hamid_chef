# أمثلة على استخدام API الأدوات مع الخدمات

## إنشاء خدمة مع الأدوات

يمكنك إرسال الأدوات مع الخدمة في نفس الطلب:

### POST `/api/chef/chef-services`

```json
{
  "chef_id": 1,
  "name": "خدمة طبخ منزلي",
  "description": "طبخ وجبات منزلية شهية",
  "service_type": "hourly",
  "hourly_rate": 50.00,
  "min_hours": 2,
  "is_active": true,
  "equipment": [
    {
      "name": "أطباق التقديم",
      "is_included": true
    },
    {
      "name": "أدوات الطبخ الأساسية",
      "is_included": true
    },
    {
      "name": "فرن الغاز",
      "is_included": false
    }
  ]
}
```

## تحديث خدمة مع الأدوات

### PUT `/api/chef/chef-services/{id}`

```json
{
  "name": "خدمة طبخ منزلي محدثة",
  "equipment": [
    {
      "id": 1,
      "name": "أطباق التقديم المحدثة",
      "is_included": true
    },
    {
      "name": "أداة جديدة",
      "is_included": false
    }
  ]
}
```

**ملاحظة**: عند التحديث:
- إذا كان للأداة `id` موجود، سيتم تحديثها
- إذا لم يكن لها `id`، سيتم إنشاؤها كأداة جديدة
- الأدوات التي لم تُرسل في القائمة سيتم حذفها

## عرض أدوات خدمة محددة

### GET `/api/chef-services/{serviceId}/equipment`

```json
{
  "success": true,
  "message": "Equipment retrieved successfully.",
  "data": [
    {
      "id": 1,
      "name": "أطباق التقديم",
      "is_included": true,
      "chef_service_id": 1,
      "created_at": "2025-12-23T12:00:00.000000Z",
      "updated_at": "2025-12-23T12:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "فرن الغاز",
      "is_included": false,
      "chef_service_id": 1,
      "created_at": "2025-12-23T12:00:00.000000Z",
      "updated_at": "2025-12-23T12:00:00.000000Z"
    }
  ]
}
```

## إدارة الأدوات بشكل منفصل

### إضافة أداة جديدة
**POST** `/api/chef/chef-services/{serviceId}/equipment`

```json
{
  "chef_service_id": 1,
  "name": "أداة جديدة",
  "is_included": true
}
```

### تحديث أداة موجودة
**PUT** `/api/chef/chef-service-equipment/{id}`

```json
{
  "name": "اسم محدث للأداة",
  "is_included": false
}
```

### حذف أداة
**DELETE** `/api/chef/chef-service-equipment/{id}`

### إدارة متعددة للأدوات
**POST** `/api/chef/chef-service-equipment/bulk-manage`

```json
{
  "chef_service_id": 1,
  "equipment": [
    {
      "id": 1,
      "name": "أداة محدثة",
      "is_included": true
    },
    {
      "name": "أداة جديدة",
      "is_included": false
    }
  ],
  "delete_ids": [3, 4]
}
```

### نسخ أدوات من خدمة أخرى
**POST** `/api/chef/chef-service-equipment/copy-from-service`

```json
{
  "to_service_id": 1,
  "from_service_id": 2,
  "equipment_ids": [5, 6]
}
```

## هيكل البيانات المبسط

الأدوات تحتوي على الحقول التالية فقط:
- `id`: معرف الأداة
- `chef_service_id`: معرف الخدمة
- `name`: اسم الأداة (مطلوب، حد أقصى 100 حرف)
- `is_included`: هل الأداة متضمنة في الخدمة أم يوفرها العميل (افتراضي: true)
- `created_at`: تاريخ الإنشاء
- `updated_at`: تاريخ آخر تحديث

## المزايا

1. **بساطة**: يمكن إرسال الأدوات مع الخدمة في طلب واحد
2. **مرونة**: يمكن إدارة الأدوات بشكل منفصل أيضاً
3. **كفاءة**: تحديث الأدوات يتم بذكاء (إنشاء/تحديث/حذف حسب الحاجة)
4. **تكامل**: جميع العمليات متاحة عبر `ChefServiceController` الموحد