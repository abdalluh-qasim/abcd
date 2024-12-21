<?php
// search.php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit(json_encode([
        'success' => false,
        'message' => 'طريقة طلب غير صحيحة',
        'debug' => 'Invalid request method'
    ]));
}

if (!isset($_POST['studentCode'])) {
    exit(json_encode([
        'success' => false,
        'message' => 'الكود مطلوب',
        'debug' => 'No student code provided'
    ]));
}

$studentCode = $_POST['studentCode'];

// التحقق من صحة الكود
if (!preg_match('/^\d{5,10}$/', $studentCode)) {
    exit(json_encode([
        'success' => false,
        'message' => 'كود غير صالح',
        'debug' => 'Invalid code format'
    ]));
}

// تحديد مجلد الصور
$gradesDir = "grades/";

// التحقق من وجود المجلد
if (!is_dir($gradesDir)) {
    exit(json_encode([
        'success' => false,
        'message' => 'خطأ في النظام: مجلد الدرجات غير موجود',
        'debug' => 'Grades directory not found'
    ]));
}

// التحقق من صلاحيات المجلد
if (!is_readable($gradesDir)) {
    exit(json_encode([
        'success' => false,
        'message' => 'خطأ في النظام: لا يمكن قراءة مجلد الدرجات',
        'debug' => 'Grades directory not readable'
    ]));
}

// البحث عن الصورة بامتدادات مختلفة
$extensions = ['jpg', 'jpeg', 'png'];
$foundImage = null;
$searchedPaths = [];

foreach ($extensions as $ext) {
    $filePath = $gradesDir . $studentCode . "." . $ext;
    $searchedPaths[] = $filePath;
    
    if (file_exists($filePath)) {
        if (!is_readable($filePath)) {
            exit(json_encode([
                'success' => false,
                'message' => 'خطأ في النظام: لا يمكن قراءة ملف النتيجة',
                'debug' => 'File exists but not readable: ' . $filePath
            ]));
        }
        $foundImage = [
            'path' => $filePath,
            'type' => $ext
        ];
        break;
    }
}

if ($foundImage) {
    echo json_encode([
        'success' => true,
        'imagePath' => $foundImage['path'],
        'debug' => 'Found image: ' . $foundImage['path']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'لم يتم العثور على نتيجة لهذا الكود',
        'debug' => [
            'searched_paths' => $searchedPaths,
            'current_dir' => getcwd(),
            'php_user' => get_current_user()
        ]
    ]);
}
?>