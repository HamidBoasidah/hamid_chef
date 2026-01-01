# Chef Availability API Documentation

## Endpoint

```
GET /api/chefs/{chefId}/availability-calendar
```

## Description

هذا الـ API يرجع بيانات شاملة عن توفر الطاهي تشمل:
- **الأيام المتاحة**: أيام العمل بدون حجوزات
- **أيام الإجازة**: الأيام التي لا يعمل فيها الطاهي
- **الأيام المحجوزة جزئياً**: أيام بها حجوزات ولكن يوجد وقت متاح
- **الأيام المحجوزة بالكامل**: أيام ممتلئة بالحجوزات
- **تفاصيل يوم محدد**: ساعات الدوام، الحجوزات، الفترات المتاحة
- **ساعات الراحة لكل خدمة**: كل خدمة لها ساعات راحة مطلوبة خاصة بها

## Calendar Date Range Logic

التقويم يعمل بالمنطق التالي:
- **تاريخ النهاية**: دائماً نهاية الشهر
- **تاريخ البداية**: التاريخ المرسل (أو اليوم)
- **الحد الأدنى**: إذا كان المتبقي من الشهر أقل من 10 أيام، يرجع للخلف لضمان 10 أيام على الأقل

### أمثلة:
| التاريخ المرسل | تاريخ البداية | تاريخ النهاية | عدد الأيام |
|---------------|--------------|--------------|-----------|
| 15 يناير | 15 يناير | 31 يناير | 17 يوم |
| 25 يناير | 22 يناير | 31 يناير | 10 أيام |
| 30 يناير | 22 يناير | 31 يناير | 10 أيام |

## Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `chefId` | integer | Yes | - | معرف الطاهي |
| `date` | string (Y-m-d) | No | today | التاريخ المطلوب (التقويم يبدأ من هذا التاريخ أو قبله لضمان 10 أيام) |

## Response Structure

```json
{
  "success": true,
  "message": "تم جلب بيانات التوفر بنجاح",
  "data": {
    "chef_id": 1,
    "chef_name": "اسم الطاهي",
    "default_rest_hours": 2,
    "calendar_start_date": "2026-01-22",
    "calendar_end_date": "2026-01-31",
    "calendar": {
      "available_days": [...],
      "off_days": [...],
      "partially_booked_days": [...],
      "fully_booked_days": [...]
    },
    "selected_date": "2026-01-25",
    "day_details": {...},
    "services": [...]
  }
}
```

## Services Object

قائمة خدمات الطاهي النشطة مع ساعات الراحة المطلوبة لكل خدمة:

```json
{
  "id": 5,
  "name": "طبخ منزلي",
  "service_type": "hourly",
  "hourly_rate": "25.00",
  "min_hours": 2,
  "package_price": null,
  "rest_hours_required": 2
}
```

**ملاحظة مهمة**: `rest_hours_required` هو عدد ساعات الراحة المطلوبة بعد انتهاء الحجز لهذه الخدمة. يمكن أن يختلف من خدمة لأخرى.

## Day Details Object

تفاصيل يوم محدد مع ساعات الراحة لكل حجز:

```json
{
  "date": "2026-01-25",
  "day_name": "Sunday",
  "day_name_ar": "الأحد",
  "is_working_day": true,
  "is_off_day": false,
  "working_hours": [
    {
      "start_time": "09:00",
      "end_time": "14:00"
    },
    {
      "start_time": "16:00",
      "end_time": "22:00"
    }
  ],
  "bookings": [
    {
      "id": 123,
      "start_time": "10:00",
      "end_time": "12:00",
      "hours_count": 2,
      "rest_hours_required": 3,
      "blocked_until": "15:00",
      "total_blocked_hours": 5,
      "booking_status": "accepted",
      "service": {
        "id": 5,
        "name": "طبخ حفلات",
        "service_type": "hourly",
        "min_hours": 2,
        "rest_hours_required": 3
      }
    }
  ],
  "available_slots": [
    {
      "start_time": "09:00",
      "end_time": "10:00",
      "duration_hours": 1
    },
    {
      "start_time": "15:00",
      "end_time": "22:00",
      "duration_hours": 7
    }
  ],
  "default_rest_hours": 2
}
```

### Booking Fields Explanation

| Field | Description |
|-------|-------------|
| `start_time` | وقت بداية الحجز |
| `end_time` | وقت نهاية الحجز (start_time + hours_count) |
| `hours_count` | عدد ساعات الحجز |
| `rest_hours_required` | ساعات الراحة المطلوبة بعد الحجز (من الخدمة) |
| `blocked_until` | الوقت المحجوز بالكامل (end_time + rest_hours_required) |
| `total_blocked_hours` | إجمالي الساعات المحجوزة (hours_count + rest_hours_required) |

## Calendar Object

### available_days
أيام العمل بدون أي حجوزات:
```json
{
  "date": "2026-01-25",
  "day_name": "Sunday",
  "day_name_ar": "الأحد",
  "bookings_count": 0,
  "availability_percentage": 100
}
```

### off_days
أيام الإجازة (لا يعمل فيها الطاهي):
```json
{
  "date": "2026-01-23",
  "day_name": "Friday",
  "day_name_ar": "الجمعة"
}
```

### partially_booked_days
أيام بها حجوزات ولكن يوجد وقت متاح:
```json
{
  "date": "2026-01-26",
  "day_name": "Monday",
  "day_name_ar": "الإثنين",
  "bookings_count": 2,
  "availability_percentage": 45.5
}
```

### fully_booked_days
أيام ممتلئة بالحجوزات (أقل من 10% متاح):
```json
{
  "date": "2026-01-27",
  "day_name": "Tuesday",
  "day_name_ar": "الثلاثاء",
  "bookings_count": 4,
  "availability_percentage": 5.2
}
```

## Important Notes

### ساعات الراحة (Rest Hours)
- **كل خدمة لها ساعات راحة خاصة بها** (`rest_hours_required` في جدول `chef_services`)
- القيمة الافتراضية: 2 ساعات (إذا لم يتم تحديد قيمة للخدمة)
- `blocked_until` = `end_time` + `rest_hours_required`
- `total_blocked_hours` = `hours_count` + `rest_hours_required`

### مثال على حساب الوقت المحجوز
```
خدمة: طبخ حفلات (rest_hours_required = 3)
حجز: من 10:00 لمدة 2 ساعات

start_time = 10:00
end_time = 12:00 (10:00 + 2 ساعات)
rest_hours_required = 3 (من الخدمة)
blocked_until = 15:00 (12:00 + 3 ساعات راحة)
total_blocked_hours = 5 (2 + 3)

الفترة المحجوزة الفعلية: 10:00 - 15:00
```

### حساب التوفر
- `availability_percentage` يحسب نسبة الوقت المتاح من إجمالي ساعات العمل
- يأخذ في الاعتبار ساعات الراحة الخاصة بكل خدمة

### أيام الإجازة
- تُحدد بناءً على جدول ساعات العمل (`chef_working_hours`)
- إذا لم يكن للطاهي ساعات عمل في يوم معين، يُعتبر يوم إجازة

## Example Request

```bash
# Get availability for chef 1, starting from January 25, 2026 (will show Jan 22-31)
curl -X GET "https://monchef.codebrains.net/api/chefs/1/availability-calendar?date=2026-01-25"

# Get availability starting from today (default)
curl -X GET "https://monchef.codebrains.net/api/chefs/1/availability-calendar"

# Get availability for specific date in middle of month
curl -X GET "https://monchef.codebrains.net/api/chefs/1/availability-calendar?date=2026-01-15"
```

## Error Responses

### Chef Not Found (404)
```json
{
  "success": false,
  "message": "الطاهي المطلوب غير موجود"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "date": ["The date must be a valid date."]
  }
}
```

## Database Schema

### chef_services table (rest_hours_required field)
```sql
ALTER TABLE chef_services 
ADD COLUMN rest_hours_required TINYINT UNSIGNED DEFAULT 2 
COMMENT 'Required rest hours after booking ends';
```

## API for Managing Service Rest Hours

### Create Service with Rest Hours
```bash
POST /api/chef/chef-services
{
  "chef_id": 1,
  "name": "طبخ حفلات",
  "service_type": "hourly",
  "hourly_rate": 30,
  "min_hours": 2,
  "rest_hours_required": 3
}
```

### Update Service Rest Hours
```bash
PUT /api/chef/chef-services/{id}
{
  "rest_hours_required": 4
}
```
