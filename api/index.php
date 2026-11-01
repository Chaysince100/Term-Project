<?php
// กำหนด Current Working Directory เป็นโฟลเดอร์หลัก เพื่อให้คำสั่ง include/require ทำงานได้อย่างสมบูรณ์
chdir(dirname(__DIR__));

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = ltrim($uri, '/');

if ($file === '' || $file === 'index.php') {
    require 'index.php';
    exit;
}

// ตรวจสอบไฟล์ปลายทาง
$target = basename($file);
if (file_exists($target) && pathinfo($target, PATHINFO_EXTENSION) === 'php') {
    require $target;
    exit;
}

// กรณีไม่พบไฟล์ ให้เปิดหน้าหลัก index.php
require 'index.php';
?>
