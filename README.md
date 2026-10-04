# Rushd

متتبع دخل ومصروف. الواجهة الإنجليزية في `rushd.html` وعنوانها Expense And Income Tracker. لا README سابق في المجلد؛ هذا الملف أول توثيق من الكود و`complete_database.sql`.

## 1 ما هو المشروع

تطبيق يسجّل حركات مالية من نوع `Income` أو `Expense`. المصروف يحمل تصنيفاً `Essential` أو `Luxury`. الواجهة الرئيسية `rushd.html` تتحدث مع `api.php` بصيغة JSON. توجد أيضاً صفحة PHP `tracker_dashboard.php` تضمّن `trackerdashboravd.php` وتعرض مجاميع.

حسابات الدخول في جدول `users` مع `password_hash` و`password_verify`.

## 2 لماذا وُجد

الكود يجمع الدخل والمصروف ويحسب في المتصفح لوحات الأرقام، ويحسب في `transactionValuescalction.php` مجموع المصروف والدخل ومصروف Luxury. اسم الملف يحوي خطأ إملاء (calction) وهو الاسم الفعلي.

## 3 المستخدمون

| المصدر | الحسابات |
| --- | --- |
| `complete_database.sql` | `admin` بريد `admin@rushd.com`، و`user` بريد `user@rushd.com`، و`test` بريد `test@rushd.com` |
| `register.php` | ينشئ مستخدماً جديداً في القاعدة التي يتصل بها ذلك الملف |
| زائر غير داخل | `api.php` لا يستدعي `requireLogin` |

تعليقات SQL تذكر كلمات نصية بجانب الإدراج. فحص محلي بـ `password_verify`:

| الصف | تعليق SQL | نتيجة الفحص |
| --- | --- | --- |
| admin | كلمة مذكورة في التعليق | لا تطابق التعليق. تطابق الكلمة `password` |
| user | كلمة مذكورة في التعليق | لا تطابق التعليق ولا الكلمة `password` ضمن فحص محلي محدود |
| test | كلمة مذكورة في التعليق | لا تطابق التعليق ولا الكلمة `password`. الهاش بصيغة bcrypt حسب `password_get_info` |

لا تُنسخ هنا سلاسل الهاش ولا كلمات التعليقات.

## 4 القدرات

- عرض الحركات: `GET api.php`.
- إضافة: `POST` JSON بحقول `type` و`description` و`amount` و`category` للمصروف.
- حذف: `DELETE` JSON بحقل `id`.
- تسجيل JSON في `register.php`: اسم 3 أحرف على الأقل، بريد، كلمة 6 أحرف على الأقل، ثم جلسة.
- دخول JSON في `login.php` بالاسم أو البريد.
- فحص جلسة `check_auth.php`.
- خروج `logout.php` وواجهة `logout.html`.
- ترتيب بالمبلغ داخل `rushd.html` (`sortByAmount`).
- مجاميع PHP في `TransactionValuesCalculation`.

## 5 كيف يعمل

`api.php` يبدأ الجلسة، يضبط JSON، ويسمح بـ CORS `Access-Control-Allow-Origin: *`. `getCurrentUserId()` قد يرجع `null`. `TransactionDAO` يتصل بقاعدة مكتوبة داخل المنشئ. إن وُجد `user_id` وعمود `user_id` تُفلتر الحركات. وإلا يُعاد كل الجدول أو يُحذف بالمعرّف فقط.

`login.php` و`register.php` وملفات الاختبار تتصل بقاعدة مختلفة عن `TransactionDAO`.

## 6 أمثلة من الكود والبيانات

تجزئة التسجيل:

```php
password_hash($password, PASSWORD_DEFAULT)
```

الدخول: `password_verify`.

أنواع الحركة في الواجهة وSQL: `Income`, `Expense`. التصنيف يُصفَّر للدخل في `api.php`:

```php
$category = ($data['type'] === 'Income') ? null : ($data['category'] ?? null);
```

بذرة الحركات (مبالغ كما في SQL): دخل 5000 و1200 و500 و800 للمستخدم 1، ومصروفات 1500 و350 و80 و120 و200 و150. للمستخدم 2: دخل 2000 و200، ومصروف 800 و300 و100.

`generate_password_hash.php` يطبع هاشات ويذكر كلمات نصية في المصدر. لا تُنسخ هنا. الملف أداة توليد وليست مسار التشغيل.

## 7 رحلة المستخدم

1. `rushd login.html` يرسل JSON إلى `login.php` عبر `rushd login.js`.
2. عند النجاح تُحفظ الجلسة.
3. `rushd.html` تستدعي `check_auth.php` ثم `GET api.php`.
4. نموذج الإضافة يرسل POST.
5. زر الحذف يرسل DELETE.
6. `logout.html` أو الرابط يستدعي الخروج.

مسار ثانٍ: `tracker_dashboard.php` يحسب مجاميع عبر `trackerdashboravd.php` و`process transation.php` / `remove transation.php` كمسار أقدم بلا عزل المستخدم الظاهر في `api.php`.

## 8 الوحدات

| الوحدة | ملفات |
| --- | --- |
| واجهة المتصفح | `rushd.html`, `rushd.css`, `rushd login.html`, `rushd login.css`, `rushd login.js`, `register.html`, `logout.html`, `gr anm.html`, `grs.css` |
| API | `api.php` |
| مسار PHP أقدم | `tracker_dashboard.php`, `trackerdashboravd.php`, `process transation.php`, `remove transation.php` |
| دومين | `Transaction.php`, `transactionDAO.php`, `transactionValuescalction.php`, `database_connection.php`, `auth.php` |
| دخول | `login.php`, `register.php`, `logout.php`, `check_auth.php` |
| اختبار | `test_connection.php`, `test_api.php`, `test_mysql_only.php`, `simple_test.php`, `list_databases.php` |
| مخطط | `complete_database.sql` |
| أداة | `generate_password_hash.php` |

## 9 الكيانات

### users

`id`, `username` فريد, `email` فريد, `password`, `created_at`.

### transaction_table

`id`, `transaction_type`, `description`, `amount` عشري (10,2), `category` يقبل NULL, `user_id` يقبل NULL.

مفتاح أجنبي `user_id` إلى `users.id` مع `ON DELETE CASCADE` في SQL. `TransactionDAO::ensureUserIdColumnExists` يحاول إضافة العمود إن غاب.

## 10 الصلاحيات

`auth.php` يعرّف `requireLogin` ويوجّه إلى `rushd login.html`. `api.php` لا يستدعيها.

إن كانت الجلسة فارغة، `getAllTransactions(null)` يعيد كل الصفوف، و`deleteTransaction` يحذف بالمعرّف فقط.

عند وجود جلسة وعمود `user_id` يُقيَّد الإدراج والحذف والقراءة بذلك المستخدم.

لا أدوار في الجدول رغم تعليق SQL الذي يسمي الصف الأول admin.

## 11 الأتمتة

`ensureUserIdColumnExists` عند إنشاء `TransactionDAO`. غير موجود: cron.

## 12 التكامل بين الوحدات

`rushd.html` تعتمد `api.php`. `api.php` تعتمد DAO و`auth.php`. DAO تعتمد `database_connection.php` بأسرار اتصال خاصة بها. الدخول يعتمد اتصالاً آخر داخل `login.php`. استنتاج من الكود: نجاح الدخول لا يعني أن `api.php` يرى نفس القاعدة.

`tracker_dashboard.php` سطر أول: `require trackerdashboravd.php` ثم HTML يعرض متغيرات المجاميع.

## 13 المصطلحات

| المصطلح | هنا |
| --- | --- |
| Income / Expense | قيم `transaction_type` |
| Essential / Luxury | قيم `category` للمصروف |
| DAO | `TransactionDAO` |
| CORS | ترويسة `*` على `api.php` |

## 14 الأسئلة الشائعة

**الدخول ينجح والقائمة فارغة أو الخطأ Database connection.**  
`login.php` يتصل بـ `php_expense_income_db` كمستخدم `root` وكلمة فارغة. `transactionDAO.php` يتصل بـ `u741730784_rushed` بمستخدم وكلمة مكتوبين في الملف. `complete_database.sql` ينشئ `u741730784_rushed`. ملفات الاختبار تذكر `php_expense_income_db`. ثلاث وجهات.

**كلمة التعليق لا تدخل.**  
فحص `password_verify` على هاش admin طابق `password` ولم يطابق تعليق SQL. صفا user وtest لم يطابقا تعليقهما في الفحص المحلي.

**الواجهة بالإنجليزية.**  
نصوص `rushd.html` إنجليزية. اسم المشروع Rushd.

## 15 مخطط المعمارية

```
rushd.html --fetch--> api.php --session--> auth.php
                         |
                         v
                  TransactionDAO
                         |
                         v
                  u741730784_rushed

login.php / register.php
                         |
                         v
                  php_expense_income_db
```

## 16 التقنيات المستخدمة

PHP، PDO، MySQL utf8mb4، HTML، CSS، JavaScript (fetch وasync)، جلسات، JSON، Font Awesome 5.15.2 من cdnjs في `rushd.html`. لا إطار PHP.

## 17 شجرة الملفات

الملفات في جذر واحد. أسماء بمسافات وأخطاء إملائية هي الأسماء الحقيقية:

- `rushd login.html`, `rushd login.js`, `rushd login.css`
- `process transation.php`, `remove transation.php`
- `trackerdashboravd.php`
- `transactionValuescalction.php`
- `gr anm.html`

## 18 الواجهة

لوحة دخل ومصروف، نموذج نوع ووصف وتصنيف ومبلغ، أزرار إضافة وإلغاء، حذف لكل صف، ترتيب بالمبلغ. روابط login وRegister وLogout تُبدَّل بعد `checkAuth`. الاتجاه في `rushd.html` هو `lang="en"` بلا `dir="rtl"`.

أيقونة تبويب غير موجودة. الأيقونات من Font Awesome عبر CDN.

## 19 الخادم

`api.php` يخفي `display_errors` ويسجل `error_log`. عند فشل إنشاء DAO تُعاد JSON فيها `message` و`error` و`detailed_error` (نص الاستثناء) و`help` يشير إلى `test_connection.php` أو `simple_test.php`.

`Access-Control-Allow-Origin: *` مع طرق GET وPOST وDELETE.

## 20 مسار الطلب

GET: قراءة حركات المستخدم أو الكل. 200 مع `success` و`data`.

POST: تحقق من النوع والوصف ومبلغ رقمي أكبر من صفر. 201 عند النجاح. 400 عند نقص البيانات.

DELETE: `id` رقمي. 200 عند تنفيذ الحذف.

OPTIONS: 200 فارغ.

## 21 قاعدة البيانات

`complete_database.sql`:

- ينشئ `u741730784_rushed` بترتيب `utf8mb4_general_ci`.
- يحذف الجدولين ثم يعيد إنشاءهما.
- يدرج 3 مستخدمين وحركات.
- `START TRANSACTION` ثم `COMMIT`.
- سطر `DROP DATABASE` معلّق.

`AUTO_INCREMENT=27` على جدول الحركات رغم أن الإدراج أقل من ذلك.

## 22 نقاط الدخول

| المسار | الوظيفة |
| --- | --- |
| `api.php` | GET/POST/DELETE حركات |
| `login.php` | POST JSON دخول |
| `register.php` | POST JSON تسجيل |
| `logout.php` | إنهاء جلسة |
| `check_auth.php` | حالة الجلسة |
| `process transation.php` | إضافة لمسار اللوحة القديمة |
| `remove transation.php` | حذف لمسار اللوحة القديمة |
| `test_*.php`, `simple_test.php`, `list_databases.php` | فحص اتصال، بعضها يطبع حالة البيئة |

## 23 المصادقة

جلسة `user_id` و`username`. الدخول يقبل الاسم أو البريد. بعد النجاح في `login.php` و`register.php` تُملأ الجلسة. لا `session_regenerate_id` في الملفين المقروءين. لا CSRF على JSON. `requireLogin` غير موصول بـ `api.php`.

## 24 الأمان الموجود فعلياً

- `password_hash` / `password_verify` في التسجيل والدخول.
- استعلامات DAO بمعاملات.
- `display_errors` مطفأ في API وDAO.
- عزل `user_id` عندما تتوفر الجلسة والعمود.

حدود ظاهرة:

- API بلا إجبار دخول، والقراءة العامة عند غياب الجلسة.
- CORS `*`.
- استجابة الخطأ قد تتضمن `detailed_error`.
- أسرار اتصال مكتوبة في `transactionDAO.php` ولا تُنسخ هنا.
- ملفات اختبار تتصل بـ root.
- `generate_password_hash.php` يطبع كلمات إن شُغّل.
- لا أيقونة موقع.

## 25 الإعدادات

| الملف | الاتصال |
| --- | --- |
| `transactionDAO.php` | localhost، قاعدة `u741730784_rushed`، مستخدم وكلمة داخل الملف |
| `login.php`, `register.php`, `simple_test.php`, `test_api.php` | localhost، `root`، كلمة فارغة، قاعدة `php_expense_income_db` |
| `list_databases.php`, `test_mysql_only.php` | `root` وكلمة فارغة |
| `complete_database.sql` | ينشئ `u741730784_rushed` |

لا `.env`.

## 26 التكاملات الخارجية

Font Awesome من `cdnjs.cloudflare.com`. غير موجود: بريد أو دفع.

## 27 المهام المجدولة

غير موجود في الملفات الحالية.

## 28 الملفات والمرفقات

لا رفع. التنسيق `rushd.css` و`rushd login.css` و`grs.css`.

## 29 السجلات

`error_log` من PHP عند أخطاء DAO وAPI. لا مجلد logs داخل المشروع.

## 30 التثبيت

1. تشغيل MySQL.
2. اختر قاعدة واحدة ووحّدها في SQL و`transactionDAO.php` و`login.php`.
3. إن اتبعت SQL الحالي، أنشئ `u741730784_rushed` بمستخدم يطابق DAO، أو غيّر DAO إلى `root` وقاعدة تنشئها بنفس المخطط.
4. شغّل PHP من مجلد المشروع وافتح `rushd.html`.
5. للدخول بحساب admin المزروع في SQL على القاعدة التي يقرأها `login.php`: انقل الصفوف إلى `php_expense_income_db` أو غيّر اتصال الدخول. كلمة admin التي يتحقق منها الهاش الحالي هي `password` وليست نص التعليق.

## 31 دليل التطوير

نقطة التوسيع `api.php` و`TransactionDAO`. الواجهة في سكربت داخل `rushd.html`. المسار القديم `process transation.php` يكرر الإضافة. توحيد اسم القاعدة يمنع انقسام الجلسة عن البيانات.

## 32 النشر

غير موثق. CORS المفتوح و`detailed_error` وأسرار الاتصال في المصدر غير مناسبة لإنتاج دون تعديل. لا ملف نشر.

## 33 النسخ الاحتياطي

غير موثق. نسخ جدولَي `users` و`transaction_table` يكفي للبيانات. الهاش يبقى قابلاً للتحقق بلا مفتاح منفصل.

## 34 استكشاف الأخطاء

| العرض | المصدر |
| --- | --- |
| Database connection failed | DAO لا يصل إلى `u741730784_rushed` أو كلمة المستخدم داخل الملف لا تطابق MySQL المحلي |
| قاعدة غير موجودة | `database_connection.php` يسرد القواعد المتاحة داخل رسالة الاستثناء |
| 500 مع detailed_error | نص PDO في JSON |
| دخول ناجح بلا بيانات | قاعدتان مختلفتان |

## 35 الاعتماديات

PHP مع PDO MySQL. متصفح حديث لـ fetch. إنترنت لتحميل Font Awesome من CDN، وإلا تظهر الواجهة بلا أيقونات الخط.

## 36 القيود

- قاعدتان في الكود.
- تعليقات كلمات SQL لا تطابق الهاش حسب الفحص.
- لا عزل إجباري.
- واجهة إنجليزية.
- ملفات اختبار بجانب التشغيل.
- هاش user وtest بلا كلمة معروفة من الفحص المحلي.

## 37 الحالة الحالية

واجهة متتبع مع API ودخول مجزأ ومخطط SQL كامل، والاتصالات غير موحدة. لوحة PHP أقدم ما زالت في المجلد.

## 38 قرارات معمارية

- JSON API للواجهة الأحدث.
- DAO مع إصلاح عمود `user_id` تلقائياً.
- حسابات بـ `PASSWORD_DEFAULT`.
- فئة حساب مجاميع منفصلة عن واجهة المتصفح التي تعيد الحساب أيضاً.
- الإبقاء على ملفات المسار القديم والاختبار داخل الجذر.

## 39 سجل التغييرات

غير موجود في الملفات الحالية.

## System Overview

متتبع دخل ومصروف بواجهة HTML وAPI PHP. الحركات تُعزل بالمستخدم عندما تتصل الواجهة والدخول بنفس القاعدة وتوجد جلسة. البذرة في `u741730784_rushed`. الدخول الافتراضي في الكود يبحث في `php_expense_income_db`.

## Quick Reference

| البند | القيمة |
| --- | --- |
| الواجهة | `rushd.html` |
| API | `api.php` |
| قاعدة SQL | `u741730784_rushed` |
| قاعدة الدخول في الكود | `php_expense_income_db` |
| حساب admin | الهاش يطابق `password` |
| حسابا user وtest | كلمة التعليق لا تطابق الهاش |

## Quick Start

1. نفّذ `complete_database.sql` على MySQL المحلي.
2. عدّل منشئ `TransactionDAO` ليتصل بمستخدم محلي تستطيع استخدامه (غالباً root بلا كلمة على XAMPP) ونفس اسم القاعدة، أو أنشئ مستخدم SQL المطابق للملف.
3. اجعل `login.php` و`register.php` يشيران إلى القاعدة نفسها.
4. `php -S localhost:8080` ثم افتح `rushd.html`.
5. ادخل باسم `admin` والكلمة `password` إذا كانت صفوف SQL هي التي يقرأها `login.php`.

## For Non-Technical Users

الشاشة تعرض الدخل والمصروف. تضيف حركة بمبلغ ووصف. المصروف يحتاج تصنيفاً أساسياً أو كمالياً. الحذف يزيل السطر. أزرار الدخول والتسجيل أعلى الصفحة.

## For Developers

ابدأ بمقارنة سلاسل الاتصال في `transactionDAO.php` و`login.php` قبل تتبع أي خطأ بيانات. `api.php` يعيد `detailed_error` عند فشل التهيئة. `requireLogin` جاهلة وغير مستخدمة في API. لا تشغّل `generate_password_hash.php` على خادم عام لأنه يطبع كلمات.
