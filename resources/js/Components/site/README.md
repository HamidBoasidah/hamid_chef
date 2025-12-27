# Landing Page Components

## 📁 الهيكل النهائي

```
resources/js/
├── Components/site/
│   ├── HeroSection.vue              ✅ قسم البطل
│   ├── FeaturesSection.vue          ✅ قسم المميزات
│   ├── HowItWorksSection.vue        ✅ قسم كيف يعمل
│   ├── WhyUsSection.vue             ✅ قسم لماذا نحن
│   ├── TestimonialsSection.vue      ✅ قسم آراء العملاء
│   ├── ContactSection.vue           ✅ قسم التواصل
│   ├── AboutUsSection.vue           ✅ قسم من نحن
│   ├── VisionMissionSection.vue     ✅ قسم الرؤية والرسالة
│   ├── TopChefsSection.vue          ✅ قسم أفضل الطهاة
│   ├── CategoriesSection.vue        ✅ قسم التصنيفات
│   ├── SiteNavbar.vue               ✅ شريط التنقل
│   ├── SiteFooter.vue               ✅ التذييل
│   ├── index.js                     ✅ ملف التصدير
│   ├── SECTIONS_README.md           📄 توثيق الأقسام
│   └── README.md                    📄 هذا الملف
│
└── Pages/Site/
    └── LandingPage.vue              ✅ الصفحة الرئيسية
```

## ✨ ما تم إنجازه

### 1. إنشاء مكونات منفصلة لكل قسم
- ✅ جميع الأقسام لها مكونات مستقلة
- ✅ سهولة الصيانة والتطوير
- ✅ إعادة استخدام المكونات

### 2. حذف الملفات القديمة
تم حذف جميع الملفات غير المستخدمة:
- ❌ CTASection.vue
- ❌ DynamicSection.vue
- ❌ SectionHeader.vue
- ❌ SiteButton.vue
- ❌ SiteCard.vue
- ❌ SiteContactForm.vue
- ❌ SiteFeatureCard.vue
- ❌ SiteGalleryItem.vue
- ❌ SiteHero.vue
- ❌ SiteInput.vue
- ❌ SiteModal.vue
- ❌ SitePanel.vue
- ❌ SitePartnersSlider.vue
- ❌ SitePortfolioItem.vue
- ❌ SiteProductCard.vue
- ❌ SiteServiceCard.vue
- ❌ SiteTestimonialCard.vue
- ❌ جميع المجلدات الفرعية في Pages/Site

### 3. تحديث LandingPage.vue
- ✅ استخدام المكونات الجديدة
- ✅ بنية نظيفة ومنظمة
- ✅ سهولة إضافة/حذف الأقسام

## 🚀 الاستخدام

### استيراد المكونات

```vue
<script setup>
import { 
  HeroSection, 
  FeaturesSection,
  WhyUsSection,
  // ... المزيد
  SiteNavbar,
  SiteFooter
} from '@/Components/site'
</script>
```

### استخدام في الصفحة

```vue
<template>
  <div>
    <SiteNavbar />
    <HeroSection :section="sections.hero" :current-lang="locale" />
    <FeaturesSection :section="sections.features" :current-lang="locale" />
    <SiteFooter />
  </div>
</template>
```

## 📋 قائمة الأقسام المتاحة

| المكون | الوصف | البيانات المطلوبة |
|--------|-------|-------------------|
| `HeroSection` | قسم البطل الرئيسي | title, description, image |
| `FeaturesSection` | عرض المميزات | features[] |
| `HowItWorksSection` | شرح آلية العمل | steps[] |
| `WhyUsSection` | أسباب الاختيار | reasons[], stats[] |
| `TestimonialsSection` | آراء العملاء | testimonials[] |
| `ContactSection` | معلومات التواصل | email, phone, address, social_links[] |
| `AboutUsSection` | من نحن | story, values[] |
| `VisionMissionSection` | الرؤية والرسالة | vision, mission, goals[] |
| `TopChefsSection` | أفضل الطهاة | note |
| `CategoriesSection` | التصنيفات | note |

## 🎨 التصميم

### الألوان الأساسية
- `teal-500` - اللون الأساسي
- `emerald-600` - اللون الثانوي
- `gray-50/900` - الخلفيات

### الأيقونات
- مكتبة: `lucide-vue-next`
- التنسيق: PascalCase (مثل `Certificate`, `Heart`)

### الرسوم المتحركة
- `animate-float` - حركة عائمة بطيئة
- `animate-float-delayed` - حركة عائمة متأخرة
- `animate-float-slow` - حركة عائمة بطيئة جداً

## 🔧 التخصيص

### إضافة قسم جديد

1. أنشئ ملف المكون:
```vue
<!-- resources/js/Components/site/NewSection.vue -->
<template>
  <section class="relative py-20 lg:py-28">
    <!-- محتوى القسم -->
  </section>
</template>

<script setup>
defineProps({
  section: Object,
  currentLang: String
})
</script>
```

2. أضفه إلى `index.js`:
```javascript
export { default as NewSection } from './NewSection.vue';
```

3. استخدمه في `LandingPage.vue`:
```vue
<NewSection :section="sections.new_section" :current-lang="currentLocale" />
```

## 📦 التبعيات

```json
{
  "lucide-vue-next": "^0.x.x",
  "@inertiajs/vue3": "^2.x.x",
  "vue": "^3.x.x"
}
```

## 🎯 الميزات

- ✅ تصميم موحد ومتناسق
- ✅ دعم كامل للغتين (عربي/إنجليزي)
- ✅ دعم الوضع الداكن
- ✅ متجاوب مع جميع الشاشات
- ✅ رسوم متحركة سلسة
- ✅ أيقونات حديثة
- ✅ كود نظيف ومنظم
- ✅ سهولة الصيانة

## 📝 ملاحظات

1. جميع المكونات تستقبل `section` و `currentLang` كـ props
2. البيانات تأتي من قاعدة البيانات عبر `LandingPageSection` model
3. المكونات مستقلة ويمكن استخدامها بشكل منفصل
4. التصميم يتبع نظام Tailwind CSS

## 🆘 الدعم

للمزيد من المعلومات، راجع:
- `SECTIONS_README.md` - توثيق تفصيلي لكل قسم
- [Lucide Icons](https://lucide.dev/)
- [Tailwind CSS](https://tailwindcss.com/)

---

**آخر تحديث:** ديسمبر 2024  
**الحالة:** ✅ جاهز للإنتاج
