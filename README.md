
# DBWOODPEACKER

## توضیحات
**DBWOODPEACKER** یک کتابخانه ساده و کارآمد برای مدیریت جداول پایگاه‌داده در PHP است. این کتابخانه امکاناتی مانند انتخاب، پیدا کردن، درج، به‌روزرسانی و حذف رکوردها را به راحتی فراهم می‌کند.

**DBWOODPEACKER** is a simple and efficient library for managing database tables in PHP. This library provides easy functionality for selecting, finding, inserting, updating, and deleting records.

## نصب
برای نصب کتابخانه، از Composer استفاده کنید:

To install the library, use Composer:

```bash
composer require woodpeacker/dbwoodpeacker
نحوه استفاده
راه‌اندازی
برای استفاده از کتابخانه، ابتدا باید یک شیء از کلاس DatabaseTable ایجاد کنید:

To use the library, first create an instance of the DatabaseTable class:

require 'vendor/autoload.php';

use Woodpeacker\DatabaseTable;

$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'username', 'password');
$table = 'your_table';
$primaryKey = 'id';

$dbTable = new DatabaseTable($pdo, $table, $primaryKey);
انتخاب فیلد
برای انتخاب یک فیلد خاص از جدول:

To select a specific field from the table:

$results = $dbTable->select('column_name');
print_r($results);
پیدا کردن رکورد
برای پیدا کردن یک رکورد با یک فیلد و مقدار خاص:

To find a record by a specific field and value:

$result = $dbTable->find('column_name', 'value');
print_r($result);
درج رکورد
برای درج رکورد جدید:

To insert a new record:

$dbTable->save([
    'column1' => 'value1',
    'column2' => 'value2'
]);
به‌روزرسانی رکورد
برای به‌روزرسانی یک رکورد موجود:

To update an existing record:

$dbTable->save([
    'id' => 1,
    'column1' => 'new_value1',
    'column2' => 'new_value2'
]);
حذف رکورد
برای حذف یک رکورد:

To delete a record:

$dbTable->delete('column_name', 'value');
مجوز
این کتابخانه تحت مجوز MIT منتشر شده است.

This library is released under the MIT License.

مشارکت
اگر می‌خواهید در توسعه این کتابخانه کمک کنید، خوشحال می‌شویم که همکاری کنید!

If you would like to contribute to the development of this library, we would be happy to have your collaboration!


### نکات پایانی
- می‌توانید در صورت نیاز بخش‌های بیشتری به این فایل اضافه کنید، مانند مثال‌های بیشتر، نحوه مدیریت خطاها و ... .
- اطمینان حاصل کنید که اطلاعات مربوط به نام کاربری و رمز عبور پایگاه داده را در مثال‌ها به درستی تنظیم کنید.
- همچنین می‌توانید آیکون‌ها یا تصاویری برای جذاب‌تر کردن README اضافه کنید.