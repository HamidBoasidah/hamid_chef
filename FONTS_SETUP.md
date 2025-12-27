# إعداد الخطوط المخصصة / Custom Fonts Setup

## 📝 الملخص / Summary

المشروع مُعد لاستخدام خطوط مخصصة (Air Strip Arabic و Ahlan)، لكن **تحتاج إلى إضافة ملفات الخطوط يدوياً**.

The project is configured to use custom fonts (Air Strip Arabic and Ahlan), but **you need to add the font files manually**.

---

## 🚀 خطوات سريعة / Quick Steps

### 1. احصل على ملفات الخطوط / Get Font Files

تحتاج إلى الملفات التالية:

**Air Strip Arabic** (للعربي):
- `AirStripArabic.woff2`
- `AirStripArabic.woff`
- `AirStripArabic.ttf`

**Ahlan** (للإنجليزي):
- `Ahlan.woff2`
- `Ahlan.woff`
- `Ahlan.ttf`

### 2. ضع الملفات في المكان الصحيح / Place Files

```bash
public/fonts/
├── AirStripArabic.woff2
├── AirStripArabic.woff
├── AirStripArabic.ttf
├── Ahlan.woff2
├── Ahlan.woff
└── Ahlan.ttf
```

### 3. أعد البناء / Rebuild

```bash
npm run build
```

---

## ⚠️ الوضع الحالي / Current Status

### قبل إضافة الخطوط / Before Adding Fonts:
- ✅ الموقع يعمل بشكل طبيعي
- ⚠️ يستخدم خط Arial كبديل
- ⚠️ تحذيرات في البناء (طبيعية)

### بعد إضافة الخطوط / After Adding Fonts:
- ✅ الخطوط المخصصة تعمل
- ✅ لا توجد تحذيرات
- ✅ التصميم كامل

---

## 🔧 الملفات المُعدة / Configured Files

الخطوط مُعدة بالفعل في:

1. **`resources/css/app.css`**
   ```css
   @font-face {
       font-family: 'Air Strip Arabic';
       src: url('/fonts/AirStripArabic.woff2') format('woff2'),
            url('/fonts/AirStripArabic.woff') format('woff'),
            url('/fonts/AirStripArabic.ttf') format('truetype');
   }
   ```

2. **`tailwind.config.js`**
   ```javascript
   fontFamily: {
       arabic: ['Air Strip Arabic', 'Arial', 'sans-serif'],
       english: ['Ahlan', 'Arial', 'sans-serif'],
   }
   ```

**لا تحتاج لتغيير أي كود!** فقط أضف ملفات الخطوط.

---

## 📍 أين تحصل على الخطوط؟ / Where to Get Fonts?

### الخيار 1: من المصمم / From Designer
اطلب ملفات الخطوط من مصمم المشروع

### الخيار 2: شراء / Purchase
- [MyFonts](https://www.myfonts.com/)
- [Adobe Fonts](https://fonts.adobe.com/)
- [Google Fonts](https://fonts.google.com/) (بدائل مجانية)

### الخيار 3: استخدام بدائل مجانية / Use Free Alternatives

إذا لم تتوفر الخطوط المخصصة، يمكنك استخدام خطوط Google Fonts:

#### تعديل `resources/css/app.css`:
```css
/* استبدل @font-face بـ: */
@import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap');
```

#### تعديل `tailwind.config.js`:
```javascript
fontFamily: {
    arabic: ['Almarai', 'Arial', 'sans-serif'],
    english: ['Poppins', 'Arial', 'sans-serif'],
}
```

---

## 🎨 استخدام الخطوط في الكود / Using Fonts in Code

الخطوط تُطبق تلقائياً حسب اللغة:

```vue
<!-- للعربي / For Arabic -->
<div class="font-arabic">النص العربي</div>

<!-- للإنجليزي / For English -->
<div class="font-english">English Text</div>
```

---

## ❓ الأسئلة الشائعة / FAQ

### س: لماذا توجد تحذيرات في البناء؟
**ج:** طبيعي! التحذيرات ستختفي بعد إضافة ملفات الخطوط.

### س: هل الموقع يعمل بدون الخطوط؟
**ج:** نعم! يستخدم Arial كبديل حتى تضيف الخطوط المخصصة.

### س: ما هو أفضل صيغة للخطوط؟
**ج:** WOFF2 هو الأفضل (أصغر حجم + دعم واسع)

### س: هل أحتاج جميع الصيغات؟
**ج:** WOFF2 كافي للمتصفحات الحديثة، لكن الصيغات الأخرى للتوافق مع المتصفحات القديمة.

---

## 📚 مراجع إضافية / Additional Resources

- [Font Formats Explained](https://css-tricks.com/understanding-web-fonts-getting/)
- [Web Font Best Practices](https://web.dev/font-best-practices/)
- [Font Loading Strategies](https://web.dev/optimize-webfont-loading/)

---

## 📞 الدعم / Support

إذا واجهت مشاكل:
1. راجع `public/fonts/README.md`
2. تأكد من أسماء الملفات صحيحة
3. تأكد من صلاحيات الملفات
4. أعد بناء المشروع: `npm run build`

---

**آخر تحديث / Last Updated**: December 24, 2025
