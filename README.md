# 🎨 PYRAMEDIA - Marketing & Media Solutions

## 📋 نظرة عامة
موقع PYRAMEDIA هو موقع وكالة تسويق ومحتوى إعلامي احترافي بتصميم عصري ومتطور، يستهدف تقديم خدمات التسويق الرقمي والأتمتة في منطقة الخليج (GCC).

## 🏗️ البنية التقنية

### التقنيات المستخدمة
- **HTML5** - هيكل الموقع
- **Tailwind CSS** - التصميم والتنسيق
- **JavaScript Vanilla** - التفاعلات والوظائف
- **PHP** - Backend للنماذج والحجوزات
- **Font Awesome 6.4.0** - الأيقونات
- **Google Fonts (Poppins)** - الخطوط

### المزايا التقنية
✅ Responsive Design - متجاوب مع جميع الشاشات
✅ Dark Mode - وضع ليلي قابل للتبديل
✅ Smooth Animations - رسوم متحركة سلسة
✅ SEO Optimized - محسّن لمحركات البحث
✅ Fast Loading - سريع التحميل

## 📁 هيكل المشروع

```
Clued-pyramedia/
├── index.html              # الصفحة الرئيسية
├── js/
│   └── main.js            # JavaScript الرئيسي
├── php/
│   ├── contact.php        # معالج نموذج الاتصال
│   ├── booking.php        # معالج حجز الاستشارات
│   └── newsletter.php     # معالج اشتراكات النشرة الإخبارية
├── images/                # مجلد الصور (يمكن إضافة صور محلية)
├── css/                   # مجلد CSS إضافي (اختياري)
├── data/                  # تخزين بيانات المشتركين
├── logs/                  # سجلات النشاطات (اختياري)
├── calendar/              # ملفات ICS للتقويم (اختياري)
└── README.md             # هذا الملف
```

## 📑 الأقسام الرئيسية

### 1️⃣ Navigation Bar
- لوجو PYRAMEDIA مع أيقونة هرمية
- قائمة تنقل كاملة
- Theme Toggle (Dark/Light Mode)
- زر "Book Free Consultation"
- قائمة موبايل منسدلة

### 2️⃣ Hero Section
- عنوان رئيسي جذاب
- Call-to-Actions واضحة
- صورة تسويقية احترافية
- تأثيرات حركية

### 3️⃣ Stats Section
- 500+ مشروع مكتمل
- 200+ عميل سعيد
- 15+ سنة خبرة
- 98% رضا العملاء

### 4️⃣ About Section
- رؤية ورسالة الشركة
- 4 مزايا رئيسية
- صورة فريق العمل

### 5️⃣ Services Section
6 خدمات شاملة:
- Digital Marketing
- Marketing Automation
- Brand Strategy
- AI Solutions
- Video Production
- Analytics & Insights

### 6️⃣ Case Studies Section
3 دراسات حالة ناجحة:
- E-Commerce: 300% Sales Growth
- Branding: Complete Rebrand Success
- Automation: 80% Efficiency Gain

### 7️⃣ Blog Section
- 3 مقالات نموذجية
- Categories & Read time
- روابط "Read More"

### 8️⃣ Contact Section
- معلومات الاتصال
- نموذج تواصل تفاعلي
- تكامل مع PHP backend

### 9️⃣ Footer
- معلومات الشركة
- روابط سريعة
- قائمة الخدمات
- روابط السوشيال ميديا
- نموذج النشرة الإخبارية

## 🎭 النوافذ المنبثقة (Modals)

### Booking Modal (حجز استشارة)
**خطوتان:**
1. **اختيار الموعد:**
   - Calendar تفاعلي
   - Time Slots (9 فترات زمنية)
   - منع الأيام الماضية

2. **معلومات العميل:**
   - الاسم والإيميل والهاتف
   - اسم الشركة (اختياري)
   - وصف كيف يمكننا المساعدة

### Service Modals
- نافذة لكل خدمة
- نموذج استفسار مخصص
- حقول خاصة حسب نوع الخدمة

## 🎨 نظام الألوان

```css
Primary Orange: #FF6B35
Secondary Orange: #FF8C42
Dark Black: #1a1a1a
Light Gray: #f5f5f5
Text Gray: #666666
```

## 🌓 Dark Mode
- Toggle switch في Navbar
- تخزين الاختيار في localStorage
- تطبيق شامل على كل الموقع

## ⚙️ الإعداد والتثبيت

### المتطلبات
- خادم ويب (Apache/Nginx)
- PHP 7.4 أو أحدث
- دعم البريد الإلكتروني (mail() function)

### خطوات التثبيت

1. **رفع الملفات:**
```bash
# رفع جميع الملفات إلى خادم الويب
```

2. **إعداد الصلاحيات:**
```bash
chmod 755 php/
chmod 755 js/
chmod 777 data/
chmod 777 logs/
chmod 777 calendar/
```

3. **تكوين البريد الإلكتروني:**
افتح ملفات PHP وقم بتحديث:
```php
$to = "info@pyramedia.ae"; // استبدل بإيميلك الفعلي
```

4. **تكوين n8n Webhooks (اختياري):**
في كل ملف PHP، قم بتحديث:
```php
$webhookUrl = "https://your-n8n-instance.com/webhook/...";
```

5. **إنشاء المجلدات المطلوبة:**
```bash
mkdir -p data logs calendar
```

## 🔧 التخصيص

### تغيير الألوان
في `index.html`، ابحث عن:
```css
.gradient-bg {
    background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
}
```

### إضافة صور محلية
ضع الصور في مجلد `images/` واستبدل روابط Unsplash:
```html
<img src="images/hero-image.jpg" alt="...">
```

### تعديل محتوى الخدمات
في `index.html`، ابحث عن قسم `Services Section` وعدّل المحتوى.

### تخصيص نموذج الإيميل
في ملفات PHP، عدّل محتوى `$emailBody`.

## 📧 إعداد البريد الإلكتروني

### استخدام SMTP (موصى به)
للحصول على موثوقية أفضل، استخدم مكتبة PHPMailer:

```bash
composer require phpmailer/phpmailer
```

ثم عدّل ملفات PHP لاستخدام SMTP بدلاً من mail().

### اختبار البريد الإلكتروني
```bash
php -r "mail('test@example.com', 'Test', 'Test message');"
```

## 🔌 التكاملات

### n8n Webhooks
1. إنشاء Workflow في n8n
2. إضافة Webhook node
3. نسخ URL الـ webhook
4. تحديث ملفات PHP بـ webhook URL
5. إزالة التعليقات من كود curl

### Google Analytics
أضف في `<head>`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
```

### WhatsApp Integration
يمكن دمجه عبر n8n workflow للإشعارات التلقائية.

## 🧪 الاختبار

### اختبار النماذج
1. افتح الموقع في المتصفح
2. جرّب نموذج الاتصال
3. جرّب حجز استشارة
4. اشترك في النشرة الإخبارية
5. تأكد من استلام الإيميلات

### اختبار Responsive
```bash
# اختبر على:
- Desktop (1920x1080)
- Tablet (768x1024)
- Mobile (375x667)
```

### اختبار Dark Mode
- اضغط على Toggle في Navbar
- تأكد من تغيير جميع العناصر
- أعد تحميل الصفحة للتأكد من الحفظ

## 📱 Responsive Breakpoints

```css
Mobile: < 768px
Tablet: 768px - 1024px
Desktop: > 1024px
```

## 🚀 التحسينات المستقبلية

- [ ] نظام مدونة كامل مع CMS
- [ ] لوحة تحكم للإدارة
- [ ] نسخة عربية (Multi-language)
- [ ] Live Chat
- [ ] نظام حسابات المستخدمين
- [ ] دمج مع CRM
- [ ] تحليلات متقدمة
- [ ] نظام دفع للخدمات

## 🔒 الأمان

### توصيات الأمان
1. **Validation:** جميع المدخلات تُفحص
2. **Sanitization:** تنظيف البيانات قبل المعالجة
3. **HTTPS:** استخدم SSL للموقع
4. **Rate Limiting:** حد من محاولات الإرسال
5. **CAPTCHA:** أضف reCAPTCHA للنماذج

### ملف .htaccess
يتضمن:
- منع الوصول للمجلدات الحساسة
- Gzip compression
- Browser caching
- Security headers

## 📊 الأداء

### تحسينات الأداء المطبقة
- ✅ CDN للمكتبات (Tailwind, Font Awesome)
- ✅ Lazy loading للصور
- ✅ Minimal HTTP requests
- ✅ Optimized animations
- ✅ Efficient JavaScript

### قياس الأداء
```bash
# استخدم Google PageSpeed Insights
# أو Lighthouse في Chrome DevTools
```

## 🐛 استكشاف الأخطاء

### النماذج لا ترسل
1. تأكد من تفعيل PHP mail()
2. فحص صلاحيات المجلدات
3. فحص logs الخادم

### Dark Mode لا يعمل
1. فحص JavaScript console
2. تأكد من تحميل main.js
3. امسح localStorage وجرب مرة أخرى

### Calendar لا يظهر
1. فحص console للأخطاء
2. تأكد من تحميل JavaScript
3. تأكد من وجود الـ modal في HTML

## 📞 الدعم

للأسئلة والدعم:
- **Email:** info@pyramedia.ae
- **Website:** https://pyramedia.ae
- **GitHub Issues:** [Create an issue]

## 📄 الترخيص

هذا المشروع مملوك لـ PYRAMEDIA. جميع الحقوق محفوظة © 2025.

---

**تم تطويره بـ ❤️ بواسطة PYRAMEDIA Team**
