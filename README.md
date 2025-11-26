## وصف المشروع

هذا المشروع هو قالب مبني على Laravel مهيأ لتطبيقات الويب الإدارية. يحتوي على نماذج Eloquent مرتبة وفق نمط Repository/Service، واجهة أمامية مبنية بـ Vite، وتصدير مسارات باستخدام Ziggy.

الملفات والميزات الهامة:

- `app/Models/BaseModel.php`: نموذج أساسي (BaseModel) ترث منه النماذج الأخرى ويحتوي على حقول مشتركة مثل `created_by`, `updated_by`, و`is_active`.
- هيكل `app/Repositories` و`app/Services` لتنظيم منطق الوصول إلى البيانات وخدمات التطبيق.
- واجهة أمامية في `resources/js` تستخدم Vite وZiggy.

## المتطلبات

- PHP >= 8.x
- Composer
- Node.js >= 16 و npm
- قاعدة بيانات (MySQL/Postgres/SQLite) — بعض أوامر Artisan قد تتطلب إعداد قاعدة بيانات صحيحة (ملاحظة حول `cache:clear`).

## إعداد المشروع (محلي)

1. انسخ المستودع وقم بالدخول للمجلد:

```bash
git clone <your-repo-url> .
cd /path/to/hamid_templetev2
```

2. تثبيت تبعيات PHP:

```bash
composer install --no-interaction --prefer-dist
```

3. نسخ ملف البيئة وضبط المتغيرات:

```bash
cp .env.example .env
# ثم افتح .env وعدل إعدادات DB وAPP_URL وCACHE_DRIVER و
```

4. إنشاء مفتاح التطبيق:

```bash
php artisan key:generate
```

5. (اختياري) إذا لم تكن تستخدم قاعدة بيانات، ضبط `CACHE_DRIVER=file` في `.env` لتجنب أخطاء عند تشغيل بعض أوامر Artisan.

6. تثبيت تبعيات الواجهة وبناء الأصول:

```bash
npm ci --no-audit --no-fund
npm run build
```

7. توليد ملفات التوزيع (إذا رغبت):

```bash
php artisan route:clear
php artisan config:clear
# ملاحظة: php artisan cache:clear قد يفشل إذا كانت إعدادات DB غير صحيحة
```

## أوامر شائعة

- تشغيل الخادم المحلي (تطوير):

```bash
php artisan serve
```

- تشغيل اختبارات PHPUnit:

```bash
./vendor/bin/phpunit
```

- تحديث أو إعادة توليد autoload:

```bash
composer dump-autoload -o
```

- إعادة توليد Ziggy (export routes):

```bash
php artisan ziggy:generate resources/js/ziggy.js
```

- بناء الواجهة (Vite):

```bash
npm run build
```

## ملاحظات وملاحظات خاصة بالمشروع

- تم إنشاء `BaseModel` في `app/Models/BaseModel.php` لتقليل التكرار في النماذج.
- تم حذف مجال Advertisement كاملًا من المشروع (موديل، ريبو، سيرفس، الكنترولات، المايجريشن، الفاكتوري، والسييدر).

## القضايا المعروفة والحلول المؤقتة

- أثناء توليد autoload قد تظهر تحذيرات PSR-4 لبعض الملفات (مثال: Requests) بسبب عدم توافق الاسماء/المسارات. يفضل فتح التحذيرات وتصحيح الـ namespace أو اسم الملف المطابق لمسار PSR-4.
- أمر `php artisan cache:clear` قد يفشل إذا لم تكن إعدادات قاعدة البيانات في `.env` صحيحة لأن بعض إعدادات الكاش قد تعتمد على DB. حل سريع: عيّن `CACHE_DRIVER=file` مؤقتًا في `.env` ثم شغّل الأمر.

## كيف أساهم أو أعدل

- لطفًا افتح فرعًا جديدًا، أجرِ تعديلاتك، ثم اصدر Pull Request. التزامات (Commits) واضحة مع رسائل وصفية ستكون مفيدة.

## ملاحظات أخيرة

هذا الملف مُحدَّث ليعكس حالة المشروع الحالية. إذا رغبت أن أعدل محتوى README بإضافة أقسام مخصصة (مثال: وثائق APIs، توضيح بنية المجلدات، أو تعليمات تشغيل محددة لخادم الإنتاج)، أخبرني ما الذي تريد إضافته وسأحدّث الملف.

---

آخر تحديث: 17 أكتوبر 2025
