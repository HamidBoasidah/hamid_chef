# Icon Picker Component

مكون قابل لإعادة الاستخدام لاختيار الأيقونات من مكتبة Lucide Icons.

## الاستخدام

### 1. استيراد المكون

```vue
import IconPicker from './IconPicker.vue'
```

### 2. استخدام المكون في القالب

```vue
<IconPicker
  v-model="item.icon"
  :placeholder="t('landing_page.select_icon')"
  :title="t('landing_page.select_icon')"
  :search-placeholder="t('landing_page.search_icon')"
  :no-icons-text="t('landing_page.no_icons_found')"
/>
```

## الخصائص (Props)

| الخاصية | النوع | الافتراضي | الوصف |
|---------|------|----------|------|
| `modelValue` | String | `''` | قيمة الأيقونة المختارة (v-model) |
| `placeholder` | String | `'Select Icon'` | النص الظاهر عند عدم اختيار أيقونة |
| `title` | String | `'Select Icon'` | عنوان نافذة اختيار الأيقونات |
| `searchPlaceholder` | String | `'Search icons...'` | نص حقل البحث |
| `noIconsText` | String | `'No icons found'` | النص الظاهر عند عدم وجود نتائج بحث |

## الأحداث (Events)

| الحدث | الوصف |
|-------|------|
| `update:modelValue` | يُطلق عند اختيار أيقونة جديدة |

## مثال كامل

```vue
<template>
  <div>
    <label>{{ t('common.icon') }}</label>
    <IconPicker
      v-model="feature.icon"
      :placeholder="t('landing_page.select_icon')"
      :title="t('landing_page.select_icon')"
      :search-placeholder="t('landing_page.search_icon')"
      :no-icons-text="t('landing_page.no_icons_found')"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import IconPicker from './IconPicker.vue'

const feature = ref({
  icon: 'Certificate',
  title_ar: '',
  title_en: ''
})
</script>
```

## الأيقونات المتاحة

المكون يستخدم مكتبة `lucide-vue-next` ويوفر أكثر من 100 أيقونة شائعة.

### أمثلة على الأيقونات:
- `Certificate` - شهادة
- `ShieldCheck` - درع مع علامة صح
- `Clock` - ساعة
- `DollarSign` - علامة الدولار
- `Users` - مستخدمون
- `Heart` - قلب
- `Star` - نجمة
- `Award` - جائزة
- وغيرها الكثير...

## ملاحظات مهمة

1. **تنسيق الأسماء**: يجب استخدام PascalCase لأسماء الأيقونات (مثل `Certificate` وليس `certificate`)
2. **التخزين**: يتم تخزين اسم الأيقونة كنص في قاعدة البيانات
3. **العرض**: يتم عرض الأيقونة باستخدام مكون Lucide الديناميكي

## الصفحات التي تستخدم IconPicker

- ✅ WhyUs.vue - لاختيار أيقونات الأسباب
- ✅ HowItWorks.vue - لاختيار أيقونات الخطوات
- Features.vue - يستخدم رفع ملفات SVG بدلاً من IconPicker

## إضافة أيقونات جديدة

لإضافة أيقونات جديدة، قم بتحديث مصفوفة `availableIcons` في ملف `IconPicker.vue`:

```javascript
const availableIcons = [
  'Certificate', 'ShieldCheck', 'Clock',
  // أضف أيقونات جديدة هنا
  'NewIcon', 'AnotherIcon'
]
```

تأكد من أن الأيقونة موجودة في مكتبة Lucide Icons.
