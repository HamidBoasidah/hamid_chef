# Landing Page Sections Components

مكونات منفصلة لكل قسم من أقسام الصفحة الرئيسية، جميعها موجودة مباشرة في مجلد `site` بدون مجلدات فرعية.

## المكونات المتاحة

### 1. HeroSection.vue
قسم البطل (Hero) - الصفحة الرئيسية الأولى

**الاستخدام:**
```vue
<HeroSection :section="heroData" :current-lang="locale" />
```

**Props:**
- `section` (Object): بيانات القسم من قاعدة البيانات
- `currentLang` (String): اللغة الحالية ('ar' أو 'en')

---

### 2. WhyUsSection.vue
قسم "لماذا نحن" - يعرض الأسباب والإحصائيات

**الاستخدام:**
```vue
<WhyUsSection :section="whyUsData" :current-lang="locale" />
```

**البيانات المطلوبة:**
```javascript
{
  title_ar: 'لماذا تختارنا',
  title_en: 'Why Choose Us',
  description_ar: 'الوصف',
  description_en: 'Description',
  additional_data: {
    reasons: [
      {
        icon: 'Certificate', // اسم أيقونة Lucide
        title_ar: 'العنوان',
        title_en: 'Title',
        description_ar: 'الوصف',
        description_en: 'Description'
      }
    ],
    stats: [
      {
        number: '500+',
        label_ar: 'عميل',
        label_en: 'Clients'
      }
    ]
  }
}
```

---

### 3. HowItWorksSection.vue
قسم "كيف يعمل" - يعرض الخطوات

**الاستخدام:**
```vue
<HowItWorksSection :section="howItWorksData" :current-lang="locale" />
```

**البيانات المطلوبة:**
```javascript
{
  additional_data: {
    steps: [
      {
        step: 1,
        icon: 'Search', // اسم أيقونة Lucide
        title_ar: 'الخطوة الأولى',
        title_en: 'First Step',
        description_ar: 'الوصف',
        description_en: 'Description'
      }
    ]
  }
}
```

---

### 4. FeaturesSection.vue
قسم المميزات - يعرض الخدمات أو المميزات

**الاستخدام:**
```vue
<FeaturesSection :section="featuresData" :current-lang="locale" />
```

**البيانات المطلوبة:**
```javascript
{
  additional_data: {
    features: [
      {
        icon: 'path/to/icon.svg', // مسار الأيقونة في storage
        title_ar: 'الميزة',
        title_en: 'Feature',
        description_ar: 'الوصف',
        description_en: 'Description'
      }
    ]
  }
}
```

---

### 5. TestimonialsSection.vue
قسم آراء العملاء

**الاستخدام:**
```vue
<TestimonialsSection :section="testimonialsData" :current-lang="locale" />
```

**البيانات المطلوبة:**
```javascript
{
  additional_data: {
    testimonials: [
      {
        name_ar: 'أحمد محمد',
        name_en: 'Ahmed Mohammed',
        comment_ar: 'التعليق',
        comment_en: 'Comment',
        rating: 5,
        avatar: 'https://example.com/avatar.jpg'
      }
    ]
  }
}
```

---

### 6. ContactSection.vue
قسم التواصل - معلومات الاتصال ونموذج

**الاستخدام:**
```vue
<ContactSection :section="contactData" :current-lang="locale" />
```

**البيانات المطلوبة:**
```javascript
{
  additional_data: {
    email: 'info@example.com',
    phone: '+967 777 777 777',
    address_ar: 'العنوان بالعربي',
    address_en: 'Address in English',
    working_hours_ar: 'السبت - الخميس: 9 ص - 5 م',
    working_hours_en: 'Sat - Thu: 9 AM - 5 PM',
    social_links: [
      {
        platform: 'facebook',
        url: 'https://facebook.com/page'
      }
    ]
  }
}
```

---

## مثال استخدام كامل

```vue
<template>
  <div>
    <SiteNavbar />
    
    <HeroSection :section="sections.hero" :current-lang="locale" />
    <WhyUsSection :section="sections.why_us" :current-lang="locale" />
    <HowItWorksSection :section="sections.how_it_works" :current-lang="locale" />
    <FeaturesSection :section="sections.features" :current-lang="locale" />
    <TestimonialsSection :section="sections.testimonials" :current-lang="locale" />
    <ContactSection :section="sections.contact" :current-lang="locale" />
    
    <SiteFooter />
  </div>
</template>

<script setup>
import { 
  HeroSection, 
  WhyUsSection, 
  HowItWorksSection,
  FeaturesSection,
  TestimonialsSection,
  ContactSection,
  SiteNavbar,
  SiteFooter
} from '@/Components/site'

const props = defineProps({
  sections: Object,
  locale: String
})
</script>
```

## الميزات

✅ **تصميم موحد**: جميع المكونات تتبع نفس نمط التصميم  
✅ **دعم اللغتين**: عربي وإنجليزي  
✅ **دعم الوضع الداكن**: جميع المكونات تدعم Dark Mode  
✅ **أيقونات Lucide**: استخدام مكتبة Lucide Icons  
✅ **متجاوب**: تصميم متجاوب مع جميع الشاشات  
✅ **رسوم متحركة**: تأثيرات حركية سلسة  

## ملاحظات مهمة

1. **الأيقونات**: استخدم أسماء أيقونات Lucide بصيغة PascalCase (مثل `Certificate`, `ShieldCheck`)
2. **الصور**: تأكد من رفع الصور إلى `storage/public`
3. **اللغة**: المكونات تدعم RTL تلقائياً عند استخدام اللغة العربية
4. **البيانات**: جميع البيانات تأتي من قاعدة البيانات عبر `LandingPageSection` model

## إضافة مكون جديد

لإضافة مكون قسم جديد:

1. أنشئ ملف جديد في `resources/js/Components/site/` (مثل `AboutSection.vue`)
2. اتبع نفس البنية والتصميم للمكونات الموجودة
3. أضف المكون إلى `index.js`:
```javascript
export { default as AboutSection } from './AboutSection.vue';
```
4. استخدم المكون في صفحتك

## الدعم

للمزيد من المعلومات، راجع:
- [Lucide Icons](https://lucide.dev/icons/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Vue 3 Documentation](https://vuejs.org/)
