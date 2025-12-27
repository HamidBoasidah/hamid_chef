# 🚀 Quick Start Guide - Landing Page System

## 📋 Overview
دليل سريع لاستخدام نظام الصفحة الرئيسية الجديد

---

## 🌐 URLs

### Public Landing Page
```
http://your-domain.com/
```

### Admin Panel
```
http://your-domain.com/admin/landing-page-sections
```

---

## 🎯 How to Use

### 1. View the Landing Page
- افتح الرابط الرئيسي: `/`
- سترى جميع الأقسام النشطة
- يمكنك التبديل بين العربية والإنجليزية من شريط التنقل

### 2. Edit Sections (Admin)
1. سجل دخول إلى لوحة التحكم
2. اذهب إلى "الصفحات الخارجية" من القائمة الجانبية
3. اختر القسم الذي تريد تعديله
4. قم بالتعديلات المطلوبة
5. احفظ التغييرات

### 3. Add Icons
- عند تعديل قسم يحتوي على أيقونات (مثل Why Us أو How It Works)
- انقر على "اختر أيقونة"
- ابحث عن الأيقونة المطلوبة
- اختر الأيقونة من القائمة
- سيتم حفظ اسم الأيقونة تلقائياً

---

## 📦 Available Sections

| القسم | المفتاح | الوصف |
|-------|---------|-------|
| Hero | `hero` | القسم الرئيسي مع الشرائح |
| Features | `features` | عرض المميزات |
| How It Works | `how_it_works` | خطوات العمل |
| Why Us | `why_us` | لماذا تختارنا + إحصائيات |
| Top Chefs | `top_chefs` | أفضل الطهاة |
| Categories | `categories` | تصنيفات المطابخ |
| Testimonials | `testimonials` | آراء العملاء |
| About Us | `about_us` | من نحن |
| Vision & Mission | `vision_mission` | الرؤية والرسالة |
| Partners | `partners` | الشركاء |
| Contact | `contact` | معلومات التواصل |

---

## 🎨 Icon System

### Available Icons (100+)
```
Certificate, ShieldCheck, Clock, DollarSign, Users, Heart,
Search, CalendarCheck, Smile, Zap, Star, Shield, ChefHat,
Calendar, Target, Eye, Award, TrendingUp, CheckCircle,
ThumbsUp, MessageCircle, Phone, Mail, MapPin, Globe,
Settings, Edit, Trash, Plus, Minus, X, Check, ArrowRight,
ArrowLeft, ArrowUp, ArrowDown, ChevronRight, ChevronLeft,
ChevronUp, ChevronDown, Menu, Home, User, Users, Building,
Briefcase, ShoppingCart, CreditCard, Package, Truck, Gift,
Tag, Bookmark, Bell, AlertCircle, Info, HelpCircle, Lock,
Unlock, Key, Eye, EyeOff, Image, File, FileText, Folder,
Download, Upload, Share, Link, ExternalLink, Copy, Clipboard,
Save, Printer, Camera, Video, Music, Mic, Volume, Play,
Pause, SkipForward, SkipBack, Repeat, Shuffle, Heart,
Star, Flag, Bookmark, ThumbsUp, ThumbsDown, MessageSquare,
Send, Inbox, Mail, Phone, Smartphone, Tablet, Laptop,
Monitor, Tv, Watch, Wifi, Bluetooth, Battery, Zap, Sun,
Moon, Cloud, CloudRain, CloudSnow, Wind, Droplet, Flame,
Coffee, Pizza, Beer, Wine, Utensils, UtensilsCrossed
```

### How to Use Icons
1. في نموذج التعديل، انقر على "اختر أيقونة"
2. ابحث عن الأيقونة (مثل: "heart", "star", "check")
3. اختر الأيقونة من النتائج
4. سيتم حفظ الاسم بصيغة PascalCase تلقائياً

---

## 🔧 Common Tasks

### Add a New Reason to "Why Us"
1. اذهب إلى Admin > Landing Pages > Why Us
2. انقر على "إضافة سبب"
3. اختر أيقونة
4. أدخل العنوان بالعربية والإنجليزية
5. أدخل الوصف بالعربية والإنجليزية
6. احفظ التغييرات

### Add a New Step to "How It Works"
1. اذهب إلى Admin > Landing Pages > How It Works
2. انقر على "إضافة خطوة"
3. اختر أيقونة
4. أدخل عنوان الخطوة بالعربية والإنجليزية
5. أدخل وصف الخطوة بالعربية والإنجليزية
6. احفظ التغييرات

### Add a New Slide to Hero
1. اذهب إلى Admin > Landing Pages > Hero
2. انقر على "إضافة شريحة"
3. أدخل العنوان والوصف
4. ارفع صورة الشريحة
5. احفظ التغييرات

### Update Contact Information
1. اذهب إلى Admin > Landing Pages > Contact
2. عدل البريد الإلكتروني، الهاتف، العنوان
3. عدل روابط التواصل الاجتماعي
4. احفظ التغييرات

---

## 🎨 Customization

### Change Colors
الألوان الأساسية في `tailwind.config.js`:
```javascript
colors: {
  primary: 'teal',
  secondary: 'emerald'
}
```

### Change Animations
الرسوم المتحركة في `LandingPage.vue`:
```css
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}
```

---

## 🐛 Troubleshooting

### الأيقونات لا تظهر
- تأكد من أن اسم الأيقونة بصيغة PascalCase
- تأكد من أن الأيقونة موجودة في مكتبة Lucide
- مثال صحيح: `Certificate`, `ShieldCheck`
- مثال خاطئ: `certificate`, `shield-check`

### الترجمة لا تعمل
- تأكد من أن مفاتيح الترجمة موجودة في `ar.json` و `en.json`
- تأكد من أن اللغة محددة بشكل صحيح في الجلسة

### القسم لا يظهر في الصفحة الرئيسية
- تأكد من أن القسم نشط (is_active = true)
- تأكد من أن القسم له ترتيب عرض (display_order)
- تأكد من أن البيانات محفوظة بشكل صحيح

---

## 📚 Documentation

### Full Documentation
- `LANDING_PAGE_REDESIGN_COMPLETE.md` - Complete project summary
- `resources/js/Components/site/README.md` - Components overview
- `resources/js/Components/site/SECTIONS_README.md` - Detailed sections guide
- `resources/js/Components/admin/landing-page-section/ICON_PICKER_README.md` - Icon picker guide

---

## 🆘 Support

### Need Help?
1. راجع التوثيق الكامل في `LANDING_PAGE_REDESIGN_COMPLETE.md`
2. راجع أمثلة الاستخدام في `SECTIONS_README.md`
3. راجع دليل الأيقونات في `ICON_PICKER_README.md`

---

**Last Updated:** December 24, 2024  
**Status:** ✅ Ready to Use
