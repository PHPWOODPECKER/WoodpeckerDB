
# DBWOODPEACKER

## توضیحات
**DBWOODPEACKER** یک کتابخانه ساده و کارآمد برای مدیریت جداول پایگاه‌داده در PHP است. این کتابخانه امکاناتی مانند انتخاب، پیدا کردن، درج، به‌روزرسانی و حذف رکوردها را به راحتی فراهم می‌کند.

**DBWOODPEACKER** is a simple and efficient library for managing database tables in PHP. This library provides easy functionality for selecting, finding, inserting, updating, and deleting records.

## نصب
برای نصب کتابخانه، از Composer استفاده کنید:

To install the library, use Composer:

composer require woodpeacker/dbwoodpeacker

```bash
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
